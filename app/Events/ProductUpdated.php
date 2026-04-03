<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    /**
     * Create a new event instance.
     */
    public function __construct($product) // Accept the updated product data as a parameter
    {
        $this->product = $product;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel // Broadcast on a channel 
    {
        return new Channel('products');
    }

    public function broadcastAs(): string // Define a custom event name for the frontend
    {
        return 'product.updated';
    }

    public function broadcastWith(): array // Define the data to be sent with the event
    {
        return [
            'product' => $this->product,
        ];
    }
}
