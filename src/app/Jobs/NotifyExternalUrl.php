<?php

namespace App\Jobs;

use App\Exceptions\NotifyExternalSystemException;
use App\Libs\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyExternalUrl
 * @package App\Jobs
 */
class NotifyExternalUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subscription;
    private $eventType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscription, $eventType)
    {
        $this->subscription = $subscription;
        $this->eventType    = $eventType;
    }

    /**
     * @param SubscriptionService $subscriptionService
     * @throws NotifyExternalSystemException
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $subscriptionService->notifyExternalSystem($this->eventType, $this->subscription);
    }
}
