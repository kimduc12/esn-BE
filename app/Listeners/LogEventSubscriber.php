<?php

namespace App\Listeners;

use App\Events\OrderCreated;

class LogEventSubscriber
{
    public function handleOrderCreated($event) {}

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            OrderCreated::class,
            [LogEventSubscriber::class, 'handleOrderCreated']
        );
    }
}
