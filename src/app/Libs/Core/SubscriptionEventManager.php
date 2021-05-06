<?php


namespace App\Libs\Core;


use App\Models\SubscriptionEvent;

class SubscriptionEventManager extends AbstractBaseCore
{
    private $subscriptionEventsModel;

    public function __construct(SubscriptionEvent $subscriptionEventsModel)
    {
        $this->subscriptionEventsModel = $subscriptionEventsModel;
    }

    public function registerEvent($subscriptionId, $status)
    {
        return $this->subscriptionEventsModel->create([
            'subscription_id' => $subscriptionId,
            'status'          => $status
        ]);
    }

}
