<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class OrderCreated
{
    use Dispatchable;
    public $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }
}
