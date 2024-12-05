<?php
namespace App\Observers;

use App\Models\Product;
use App\Events\ProductEvent;

class ProductObserver
{
    public function created(Product $product)
    {
        ProductEvent::dispatch($product);
    }

    public function updated(Product $product)
    {
        ProductEvent::dispatch($product);
    }
}
