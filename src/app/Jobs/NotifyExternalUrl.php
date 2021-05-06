<?php

namespace App\Jobs;

use App\Libs\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $subscriptionService->notifyExternalSystem($this->eventType, $this->subscription);
    }
}
