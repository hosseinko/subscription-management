<?php


namespace App\Libs\Core;


use App\Models\SubscriptionEvent;

/**
 * Class SubscriptionEventManager
 * @package App\Libs\Core
 */
class SubscriptionEventManager extends AbstractBaseCore
{
    private $subscriptionEventsModel;

    /**
     * SubscriptionEventManager constructor.
     * @param SubscriptionEvent $subscriptionEventsModel
     */
    public function __construct(SubscriptionEvent $subscriptionEventsModel)
    {
        $this->subscriptionEventsModel = $subscriptionEventsModel;
    }

    /**
     * @param $subscriptionId
     * @param $status
     * @return mixed
     */
    public function registerEvent($subscriptionId, $status)
    {
        return $this->subscriptionEventsModel->create([
            'subscription_id' => $subscriptionId,
            'status'          => $status
        ]);
    }
}
