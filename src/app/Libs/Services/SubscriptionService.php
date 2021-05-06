<?php


namespace App\Libs\Services;


use App\Enums\ErrorCodes;
use App\Enums\SubscriptionStatus;
use App\Exceptions\ApplicationCredentialsNotFoundException;
use App\Exceptions\HitRateLimitException;
use App\Exceptions\InvalidApplicationCredentialsException;
use App\Exceptions\NotifyExternalSystemException;
use App\Exceptions\ResourceNotFoundException;
use App\Jobs\RetryStatusCheck;
use App\Libs\Core\ApplicationManager;
use App\Libs\Core\DeviceManager;
use App\Libs\Core\SubscriptionManager;
use App\Libs\Market\MarketManagerFactory;
use App\Models\SubscriptionEvent;
use App\Objects\Reports\EventsReport;
use Carbon\Carbon;
use Http;
use Throwable;

/**
 * Class SubscriptionService
 * @package App\Libs\Services
 */
class SubscriptionService extends AbstractBaseService
{
    private $applicationManager;
    private $deviceManager;
    private $subscriptionManager;
    private $subscriptionEventsModel;

    /**
     * SubscriptionService constructor.
     * @param ApplicationManager $applicationManager
     * @param DeviceManager $deviceManager
     * @param SubscriptionManager $subscriptionManager
     * @param SubscriptionEvent $subscriptionEventsModel
     */
    public function __construct(
        ApplicationManager $applicationManager,
        DeviceManager $deviceManager,
        SubscriptionManager $subscriptionManager,
        SubscriptionEvent $subscriptionEventsModel
    ) {
        $this->applicationManager  = $applicationManager;
        $this->deviceManager       = $deviceManager;
        $this->subscriptionManager = $subscriptionManager;

        $this->subscriptionEventsModel = $subscriptionEventsModel;
    }

    /**
     * @param $appUuid
     * @param $deviceUuid
     * @param $os
     * @param $lang
     * @return false|string
     * @throws ResourceNotFoundException
     */
    public function register($appUuid, $deviceUuid, $os, $lang)
    {
        $application = $this->applicationManager->getApplicationByUuid($appUuid);

        $device = $this->deviceManager->firstOrCreateDevice($deviceUuid, $os, $lang);

        if ($token = $this->subscriptionManager->getSubscriptionToken($application->id, $device->id)) {
            return $token;
        }

        $subscription = $this->subscriptionManager->createSubscription($application->id, $device->id);

        return $subscription->token;
    }

    /**
     * @param $clientToken
     * @param $receipt
     * @return mixed
     * @throws ApplicationCredentialsNotFoundException
     * @throws InvalidApplicationCredentialsException
     * @throws ResourceNotFoundException|Throwable
     */
    public function purchase($clientToken, $receipt)
    {
        $subscription = $this->subscriptionManager->getSubscriptionByClientToken($clientToken);

        $application = $this->applicationManager->getApplicationById($subscription->application_id);
        $device      = $this->deviceManager->getDeviceById($subscription->device_id);

        $this->validateMarketCredentials($device->os, $application);

        $marketManager = MarketManagerFactory::make(
            $device->os,
            $application->market_credentials[$device->os]['username'],
            $application->market_credentials[$device->os]['password']
        );

        $response = $marketManager->purchase($receipt);

        $this->subscriptionManager->updateSubscription($subscription->id,
            $receipt,
            SubscriptionStatus::ACTIVE,
            $response['expire_date']);

        return $response['expire_date'];
    }

    /**
     * @param $clientToken
     * @return array
     * @throws ResourceNotFoundException
     */
    public function checkSubscription($clientToken): array
    {
        $result       = ['status' => '', 'expire_date' => null];
        $subscription = $this->subscriptionManager->getSubscriptionByClientToken($clientToken);

        if ($subscription->subscription_status == SubscriptionStatus::CANCELED) {
            $result['status'] = SubscriptionStatus::CANCELED;
        } else {
            $result['expire_date'] = Carbon::createFromTimestamp($subscription->expire_date)->toDateTimeString();
            if (Carbon::now()->greaterThan(Carbon::createFromTimestamp($subscription->expire_date))) {
                $result['status'] = SubscriptionStatus::EXPIRED;
            } else {
                $result['status'] = SubscriptionStatus::ACTIVE;
            }
        }

        return $result;
    }

    /**
     * @param $page
     * @param $perPage
     * @param $filters
     * @return EventsReport
     */
    public function generateEventsReport($page, $perPage, $filters): EventsReport
    {
        return $this->subscriptionEventsModel->generateReport($page, $perPage, $filters);
    }

    /**
     * @param $osType
     * @param $application
     * @return void
     * @throws ApplicationCredentialsNotFoundException
     * @throws InvalidApplicationCredentialsException
     */
    private function validateMarketCredentials($osType, $application): void
    {
        $marketCredentials = $application->market_credentials;

        if (!isset($marketCredentials[$osType])) {
            throw new ApplicationCredentialsNotFoundException(__("errors.error_code") . ': ' . ErrorCodes::APP_CREDENTIALS_NOT_FOUND,
                500);
        }

        if (!isset($marketCredentials[$osType]['username']) || !isset($marketCredentials[$osType]['password'])) {
            throw new InvalidApplicationCredentialsException(__("errors.error_code") . ': ' . ErrorCodes::APP_CREDENTIALS_INVALID,
                500);
        }

    }

    /**
     * @param $os
     */
    public function checkSubscriptions($os)
    {
        $page    = 1;
        $perPage = 1000;

        $marketManager = MarketManagerFactory::make($os, null, null);

        while ($rows = $this->subscriptionManager->getSubscriptionsByOs($os, $page, $perPage)) {
            foreach ($rows as $row) {
                try {
                    $this->validateMarketCredentials($os, $row->application);

                    $marketManager->setCredentials(
                        $row->application->market_credentials[$os]['username'],
                        $row->application->market_credentials[$os]['password']
                    );

                    $response = $marketManager->checkSubscription($row->receipt);

                    if ($response['status'] == SubscriptionStatus::CANCELED) {
                        $this->subscriptionManager->updateSubscription(
                            $row->id,
                            $row->receipt,
                            SubscriptionStatus::CANCELED,
                            null
                        );
                    }

                    unset($response);
                } catch (Throwable $exception) {
                    if ($exception instanceof HitRateLimitException) {
                        RetryStatusCheck::dispatch($row)->onQueue($os . '_failed_check');
                    }
                }
            }

            $page++;
        }
    }

    /**
     * @param $subscriptionStatus
     * @param $subscription
     * @return bool
     * @throws NotifyExternalSystemException
     */
    public function notifyExternalSystem($subscriptionStatus, $subscription): bool
    {
        $application = $subscription->application;
        $device      = $subscription->device;

        $response = Http::post($application->event_endpoint_url, [
            'app_id'      => $application->uuid,
            'device_id'   => $device->uuid,
            'status'      => $subscriptionStatus,
            'expire_date' => $subscriptionStatus != SubscriptionStatus::CANCELED ? $subscription->expire_date : null
        ]);

        if ($response->status() != 200) {
            throw new NotifyExternalSystemException("failed", $response->status());
        }

        return true;
    }
}
