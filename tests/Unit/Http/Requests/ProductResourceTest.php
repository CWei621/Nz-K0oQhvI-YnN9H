<?php

namespace Tests\Unit\Http\Resources;

use Tests\TestCase;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Foundation\Testing\WithFaker;

class ProductResourceTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function resource_has_correct_format()
    {
        $product = Product::factory()->make([
            'id' => 1,
            'name' => 'Test Product',
            'price' => 100,
            'stock' => 10,
            'is_active' => true
        ]);

        $resource = (new ProductResource($product))->toArray(request());

        $this->assertEquals([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'image_path' => $product->image_path,
            'is_active' => (bool) $product->is_active,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ], $resource);
    }
}
