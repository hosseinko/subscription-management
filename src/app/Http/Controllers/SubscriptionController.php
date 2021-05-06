<?php

namespace App\Http\Controllers;

use App\Enums\OsTypes;
use App\Libs\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{

    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
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

            $clientToken = $this->subscriptionService->register($appUuid, $deviceUuid, $os, $lang);

            return response()->json([
                'message'      => __("messages.registration_completed_successfully"),
                'client_token' => $clientToken
            ], 201);

        } catch (\Throwable $exception) {
            return $this->handleExceptions($exception,
                __('errors.registration_failed'),
                'register_error.log',
                $request->all()
            );
        }
    }

    public function purchase(Request $request)
    {
        try {

            $this->validate($request, [
                'client-token' => 'required|string|alpha_num',
                'receipt'      => 'required|string|alpha_num'
            ]);

            $clientToken = $request->input('client-token');
            $receipt     = $request->input('receipt');

            $expireDate = $this->subscriptionService->purchase($clientToken, $receipt);

            return response()->json([
                'message'     => __("messages.purchase_completed_successfully"),
                'expire_date' => $expireDate
            ]);

        } catch (\Throwable $exception) {
            return $this->handleExceptions($exception,
                __('errors.purchase_failed'),
                'purchase_error.log',
                $request->all()
            );
        }
    }

    public function getSubscriptionReport(Request $request)
    {

    }

    public function checkSubscription($clientToken)
    {
        try {
            $result = $this->subscriptionService->checkSubscription($clientToken);

            return response()->json($result);
        } catch (\Throwable $exception) {
            return $this->handleExceptions($exception,
                __('errors.check_subscription_failed'),
                'check_subscription_error.log',
                [
                    'client_token' => $clientToken
                ]
            );
        }
    }

    public function subscriptionChanged()
    {

    }
}
