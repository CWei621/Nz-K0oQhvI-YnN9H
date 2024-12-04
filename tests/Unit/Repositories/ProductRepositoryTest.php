<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository(new Product());
    }

    /** @test */
    public function it_can_create_product()
    {
        $data = Product::factory()->make()->toArray();

        $product = $this->repository->create($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /** @test */
    public function it_can_find_product_by_image()
    {
        $product = Product::factory()->create([
            'image_path' => 'storage/products/test.jpg'
        ]);

        $found = $this->repository->findByImage('test.jpg');

        $this->assertEquals($product->id, $found->id);
    }
}
