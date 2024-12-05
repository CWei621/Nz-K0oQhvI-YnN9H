<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;
use App\Services\ProductCacheService;

class ProductController extends Controller
{
    use ApiResponse;

    protected $productService;
    protected $cacheService;

    public function __construct(
        ProductService $productService,
        ProductCacheService $cacheService
    ) {
        $this->productService = $productService;
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        $products = $this->cacheService->getAllProducts(
            fn() => $this->productService->getAllProducts()
        );
        return $this->success(ProductResource::collection($products));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->handleImageUpload($request->file('image'));
        }

        $product = $this->productService->createProduct($validated);
        $this->cacheService->clearProductCaches();

        return $this->success(new ProductResource($product), 201);
    }

    public function show(int $id)
    {
        $product = $this->cacheService->getProduct($id,
            fn() => $this->productService->getProduct($id)
        );
        return $this->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        $product = $this->productService->getProduct($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $oldImage = basename($product->image_path);
            $this->deleteOldImage($product->image_path);
            $validated['image_path'] = $this->handleImageUpload($request->file('image'));
            $this->cacheService->clearProductImageCache($oldImage);
        }

        $product = $this->productService->updateProduct($id, $validated);
        $this->cacheService->clearProductCaches($id);

        return $this->success(new ProductResource($product));
    }

    public function destroy(int $id)
    {
        $product = $this->productService->getProduct($id);
        if ($product->image_path) {
            $filename = basename($product->image_path);
            $this->deleteOldImage($product->image_path);
            $this->cacheService->clearProductImageCache($filename);
        }

        $this->cacheService->clearProductCaches($id);
        $this->productService->deleteProduct($id);

        return $this->success(null, 204);
    }

    public function getImage(string $filename)
    {
        $filename = basename($filename);

        $product = $this->cacheService->getProductImage($filename,
            fn() => $this->productService->findProductByImage($filename)
        );

        if (!$product) {
            return response()->json(['message' => '圖片不存在'], Response::HTTP_NOT_FOUND);
        }

        $path = "products/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            $this->cacheService->clearProductImageCache($filename);
            return response()->json(['message' => '圖片遺失'], Response::HTTP_NOT_FOUND);
        }

        $lastModified = Storage::disk('public')->lastModified($path);
        $etag = md5($lastModified . $filename);

        if (request()->header('If-None-Match') === $etag) {
            return response()->noContent(Response::HTTP_NOT_MODIFIED);
        }

        return response(Storage::disk('public')->get($path))
            ->header('Content-Type', File::mimeType(Storage::disk('public')->path($path)))
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('ETag', $etag)
            ->header('Last-Modified', gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
    }

    // 保留原有的 handleImageUpload 和 deleteOldImage 方法
    protected function handleImageUpload($image): string
    {
        $path = $image->store('products', 'public');
        return asset('storage/' . $path);
    }

    protected function deleteOldImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists(str_replace('storage/', '', $imagePath))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $imagePath));
        }
    }
}
