<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\OrderCreated;
use App\Events\ShippingCreated;
use App\Listeners\LogEventSubscriber;
use App\Listeners\IncreaseTotalOrderUser;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderCreated::class => [
            IncreaseTotalOrderUser::class,
        ],
        ShippingCreated::class => [

        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        LogEventSubscriber::class,
    ];
}
