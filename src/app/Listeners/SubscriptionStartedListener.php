<?php

namespace App\Listeners;

use App\Enums\SubscriptionEvents;
use App\Events\SubscriptionStartedEvent;
use App\Libs\Core\SubscriptionEventManager;

class SubscriptionStartedListener
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
     * @param SubscriptionStartedEvent $event
     */
    public function handle(SubscriptionStartedEvent $event)
    {
        $this->subscriptionEventManager->registerEvent($event->subscription->id, SubscriptionEvents::STARTED);
    }
}
