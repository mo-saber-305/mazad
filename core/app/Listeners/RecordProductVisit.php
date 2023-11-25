<?php

namespace App\Listeners;

use App\Events\ProductVisited;
use App\Models\ProductVisit;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordProductVisit implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     *
     * @param \App\Events\ProductVisited $event
     * @return void
     */
    public function handle(ProductVisited $event)
    {
        // Check if the user has already visited this product
        if (!ProductVisit::where('user_id', $event->userId)->where('product_id', $event->productId)->exists()) {
            ProductVisit::create([
                'user_id' => $event->userId,
                'product_id' => $event->productId,
            ]);
        }

    }
}
