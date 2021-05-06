<?php

namespace App\Jobs;

use App\Enums\SubscriptionStatus;
use App\Libs\Core\SubscriptionManager;
use App\Libs\Logger;
use App\Libs\Market\MarketManagerFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RetryStatusCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subscription;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SubscriptionManager $subscriptionManager)
    {
        try {
            $marketManager = MarketManagerFactory::make(
                $this->subscription->device->os,
                $this->subscription->application->market_credentials[$this->subscription->device->os]['username'],
                $this->subscription->application->market_credentials[$this->subscription->device->os]['password']
            );

            $response = $marketManager->checkSubscription($this->subscription->receipt);

            if ($response['status'] == SubscriptionStatus::CANCELED) {
                $subscriptionManager->updateSubscription(
                    $this->subscription->id,
                    $this->subscription->receipt,
                    SubscriptionStatus::CANCELED,
                    null
                );
            }
        } catch (Throwable $exception) {
            Logger::logDebug('check_subscription_job_failed.log',
                'Unable to check subscription status', [
                    'message' => $exception->getMessage(),
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                ]);
        }
    }
}
