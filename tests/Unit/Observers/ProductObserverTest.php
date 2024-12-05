<?php
// tests/Unit/Observers/ProductObserverTest.php
namespace Tests\Unit\Observers;

use Tests\TestCase;
use App\Models\Product;
use App\Events\ProductEvent;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake([ProductEvent::class]);
    }

    /** @test */
    public function it_dispatches_event_when_product_is_created()
    {
        $productData = [
            'name' => 'Test Product'
        ];

        $product = Product::factory()->create($productData);

        Event::assertDispatched(ProductEvent::class, function ($event) use ($product) {
            // 確認事件內容完整性
            return $event->product->id === $product->id
                && $event->product->name === $product->name
                && $event instanceof ProductEvent
                && $event->product->name === 'Test Product';
        });

        Event::assertDispatchedTimes(ProductEvent::class, 1);
    }

    /** @test */
    public function it_dispatches_event_when_product_is_updated()
    {
        $product = Product::factory()->create();
        $product->update(['name' => 'Updated Name']);

        // Assert event dispatched
        Event::assertDispatched(ProductEvent::class, function ($event) use ($product) {
            return $event->product->id === $product->id
                && $event->product->name === 'Updated Name';
        });

        Event::assertDispatchedTimes(ProductEvent::class, 2);
    }
}
