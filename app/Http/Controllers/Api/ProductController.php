<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;

    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = Cache::remember('products.all', 3600, function () {
            return $this->productRepository->all();
        });

        return $this->success(ProductResource::collection($products));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->handleImageUpload($request->file('image'));
        }

        $product = $this->productRepository->create($validated);
        $this->clearProductCaches();

        return $this->success(new ProductResource($product), 201);
    }

    public function show(int $id)
    {
        $product = Cache::remember("products.{$id}", 3600, function () use ($id) {
            return $this->productRepository->findOrFail($id);
        });

        return $this->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        $product = $this->productRepository->findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteOldImage($product->image_path);
            $validated['image_path'] = $this->handleImageUpload($request->file('image'));
        }

        $product = $this->productRepository->update($id, $validated);
        $this->clearProductCaches($id);

        return $this->success(new ProductResource($product));
    }

    public function destroy(int $id)
    {
        $product = $this->productRepository->findOrFail($id);

        if ($product->image_path) {
            $this->deleteOldImage($product->image_path);
        }

        $this->productRepository->delete($id);
        $this->clearProductCaches($id);

        return $this->success(null, 204);
    }

    public function getImage(string $filename)
    {
        $filename = basename($filename);
        $cacheKey = "product.image.{$filename}";

        $product = Cache::remember($cacheKey, 3600, function () use ($filename) {
            return $this->productRepository->findByImage($filename);
        });

        if (!$product) {
            return response()->json(['message' => '圖片不存在'], Response::HTTP_NOT_FOUND);
        }

        $path = "products/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            Cache::forget($cacheKey);
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

    protected function handleImageUpload($image): string
    {
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('products', $filename, 'public');
        return 'storage/' . $path;
    }

    protected function deleteOldImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists(str_replace('storage/', '', $imagePath))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $imagePath));
        }
    }

    protected function clearProductCaches(int $id = null): void
    {
        Cache::forget('products.all');

        if ($id) {
            Cache::forget("products.{$id}");
            $product = $this->productRepository->find($id);
            if ($product && $product->image_path) {
                $filename = basename($product->image_path);
                Cache::forget("product.image.{$filename}");
            }
        }
    }
}
