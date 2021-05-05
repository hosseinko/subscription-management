<?php

namespace App\Http\Controllers;

use App\Enums\OsTypes;
use App\Libs\SubscriptionManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{

    private $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }


    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'uID'      => 'required|uuid',
                'appID'    => 'required|uuid',
                'os'       => ['required', Rule::in(OsTypes::toArray())],
                'language' => 'required|string|alpha|max:2'
            ]);

            $deviceUuid = $request->input('uID');
            $appUuid    = $request->input('appID');
            $os         = $request->input('os');
            $lang       = $request->input('language');

            $clientToken = $this->subscriptionManager->register($appUuid, $deviceUuid, $os, $lang);

            return response()->json([
                'client_token' => $clientToken
            ], 201);

        } catch (\Throwable $exception) {
            return $this->handleExceptions($exception,
                __('errors.client_registration_failed'),
                'register_client.log',
                $request->all()
            );
        }
    }

    public function purchase()
    {

    }

    public function checkSubscription()
    {

    }

    public function subscriptionChanged()
    {

    }
}
