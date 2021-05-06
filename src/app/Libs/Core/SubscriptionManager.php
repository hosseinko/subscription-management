<?php


namespace App\Libs\Core;

use App\Enums\SubscriptionEvents;
use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionCanceledEvent;
use App\Events\SubscriptionRenewedEvent;
use App\Events\SubscriptionStartedEvent;
use App\Jobs\NotifyExternalUrl;
use App\Libs\Repositories\Cache\ApplicationsCacheRepository;
use App\Libs\Token;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionManager extends AbstractBaseCore
{
    private $subscriptionModel;
    private $cacheRepository;
    private $tokenObj;

    public function __construct(
        Subscription $subscriptionModel,
        ApplicationsCacheRepository $applicationsCacheRepository,
        Token $tokenObj
    ) {
        $this->subscriptionModel = $subscriptionModel;
        $this->cacheRepository   = $applicationsCacheRepository;
        $this->tokenObj          = $tokenObj;
    }

    public function getSubscriptionToken($applicationId, $deviceId)
    {
        $cacheKey = "token:$deviceId:$applicationId";
        if ($token = $this->cacheRepository->fetch($cacheKey)) {
            return $token;
        }

        $subscriptionRow = $this->subscriptionModel->alreadyRegistered($applicationId, $deviceId);
        if ($subscriptionRow) {
            $this->cacheRepository->store($cacheKey, $subscriptionRow->token);
            return $subscriptionRow->token;
        }

        return false;
    }

    public function createSubscription($applicationId, $deviceId)
    {
        $subscription = $this->subscriptionModel->create([
            'application_id' => $applicationId,
            'device_id'      => $deviceId,
            'token'          => $this->tokenObj->generateToken(config('security.client_token.length'))
        ]);

        $this->cacheRepository->store("token:$deviceId:$applicationId", $subscription->token);
        $this->cacheRepository->store("subscription:{$subscription->token}", json_encode($subscription->toArray()));

        return $subscription;
    }

    public function getSubscriptionByClientToken($clientToken)
    {
        $cacheKey = "subscription:$clientToken";
        if ($subscription = $this->cacheRepository->fetch($cacheKey)) {
            return json_decode($subscription);
        }

        $subscription = $this->subscriptionModel->getSubscriptionByClientToken($clientToken);
        $this->cacheRepository->store("subscription:{$clientToken}", json_encode($subscription->toArray()));

        return $subscription;
    }

    public function updateSubscription($subscriptionId, $receipt, $status, $expireDate)
    {
        $subscription   = $this->subscriptionModel->getSubscriptionById($subscriptionId);
        $previousStatus = $subscription->status;

        $subscription->receipt             = $receipt;
        $subscription->subscription_status = $status;
        $subscription->expire_date         = $expireDate;
        $subscription->save();

        $this->cacheRepository->store("subscription:{$subscription->token}", json_encode($subscription->toArray()));

        if ($previousStatus == null && $status == SubscriptionStatus::ACTIVE) {
            SubscriptionStartedEvent::dispatch($subscription);
            NotifyExternalUrl::dispatch(SubscriptionEvents::STARTED, $subscription)->onQueue('notify_started');
        } else {
            if ($status == SubscriptionStatus::ACTIVE) {
                SubscriptionRenewedEvent::dispatch($subscription);
                NotifyExternalUrl::dispatch(SubscriptionEvents::RENEWED, $subscription)->onQueue('notify_renewed');
            } elseif ($status == SubscriptionStatus::CANCELED) {
                SubscriptionCanceledEvent::dispatch($subscription);
                NotifyExternalUrl::dispatch(SubscriptionEvents::CANCELED, $subscription)->onQueue('notify_canceled');
            }
        }
    }

    public function getSubscriptionsByOs($os, $page, $recordsPerPage)
    {
        $now = Carbon::now()->toDateTimeString();

        return $this->subscriptionModel->with(['application', 'device'])
                                       ->whereHas('device', function ($query) use ($os) {
                                           return $query->where('os', $os);
                                       })
                                       ->whereNotNull('subscription_status')
                                       ->whereDate('expire_date', '<', $now)
                                       ->where('subscription_status', '!=', SubscriptionStatus::CANCELED)
                                       ->orderBy('id', 'ASC')
                                       ->limit($recordsPerPage)
                                       ->offset(ceil(($page - 1) / $recordsPerPage))
                                       ->get();
    }
}
