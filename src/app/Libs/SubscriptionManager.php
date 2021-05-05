<?php


namespace App\Libs;


use App\Models\Application;
use App\Models\Device;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;

class SubscriptionManager extends BaseLib
{
    private $appModel;
    private $devicesModel;
    private $subscriptionModel;
    private $subscriptionEventsModel;
    private $tokenManager;

    public function __construct(
        Application $appModel,
        Device $devicesModel,
        Subscription $subscriptionModel,
        SubscriptionEvent $subscriptionEventsModel,
        Token $tokenManager
    ) {
        $this->appModel                = $appModel;
        $this->devicesModel            = $devicesModel;
        $this->subscriptionModel       = $subscriptionModel;
        $this->subscriptionEventsModel = $subscriptionEventsModel;
        $this->tokenManager            = $tokenManager;
    }

    public function register($appUuid, $deviceUuid, $os, $lang)
    {
        $application = $this->appModel->getApplicationByUuid($appUuid);

        $device = $this->devicesModel->firstOrCreate(
            ['uuid' => $deviceUuid],
            [
                'os'   => $os,
                'lang' => $lang
            ]
        );

        if ($subscription = $this->subscriptionModel->alreadyRegistered($application->id, $device->id)) {
            return $subscription->token;
        }

        $subscription = $this->subscriptionModel->create([
            'application_id' => $application->id,
            'device_id'      => $device->id,
            'token'          => $this->tokenManager->generateToken(config('security.client_token.length'))
        ]);

        return $subscription->token;
    }


}
