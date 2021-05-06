<?php

namespace App\Listeners;

use App\Enums\SubscriptionEvents;
use App\Events\SubscriptionCanceledEvent;
use App\Libs\Core\SubscriptionEventManager;

class SubscriptionCanceledListener
{
    private $subscriptionEventManager;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SubscriptionEventManager $subscriptionEventManager)
    {
        $this->subscriptionEventManager = $subscriptionEventManager;
    }

    /**
     * @param SubscriptionCanceledEvent $event
     */
    public function handle(SubscriptionCanceledEvent $event)
    {
        $this->subscriptionEventManager->registerEvent($event->subscription->id, SubscriptionEvents::CANCELED);
    }
}
