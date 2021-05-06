<?php

namespace App\Providers;

use App\Events\SubscriptionCanceledEvent;
use App\Events\SubscriptionRenewedEvent;
use App\Events\SubscriptionStartedEvent;
use App\Listeners\SubscriptionCanceledListener;
use App\Listeners\SubscriptionRenewedListener;
use App\Listeners\SubscriptionStartedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class                => [
            SendEmailVerificationNotification::class,
        ],
        SubscriptionStartedEvent::class  => [
            SubscriptionStartedListener::class
        ],
        SubscriptionRenewedEvent::class  => [
            SubscriptionRenewedListener::class
        ],
        SubscriptionCanceledEvent::class => [
            SubscriptionCanceledListener::class
        ]
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
}
