<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Cache::flush();

        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'stock' => 10,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_show_a_product()
    {
        $response = $this->getJson("/api/products/{$this->product->id}");

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $this->product->id,
                    'name' => 'Test Product',
                    'description' => 'Test Description',
                    'price' => '100.00',
                    'stock' => 10,
                    'is_active' => true
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_when_product_not_found()
    {
        $response = $this->getJson('/api/products/999');

        $response
            ->assertNotFound()
            ->assertJson([
                'status' => 'fail',
                'data' => 'Not found'
            ]);
    }
}
