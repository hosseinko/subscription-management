<?php

namespace App\Http\Controllers;

use App\Enums\OsTypes;
use App\Enums\SubscriptionEvents;
use App\Enums\SubscriptionStatus;
use App\Libs\Logger;
use App\Libs\Services\SubscriptionService;
use Faker\Generator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class SubscriptionController
 * @package App\Http\Controllers
 */
class SubscriptionController extends Controller
{

    private $subscriptionService;

    /**
     * SubscriptionController constructor.
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function purchase(Request $request): JsonResponse
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSubscriptionReport(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'status'      => ['nullable', Rule::in(SubscriptionEvents::toArray())],
                'app_uuid'    => 'nullable|uuid',
                'device_uuid' => 'nullable|uuid',
                'os'          => ['nullable', Rule::in(OsTypes::toArray())],
                'day'         => 'nullable|date_format:Y-m-d',
                'page'        => 'nullable|numeric|min:1',
                'per_page'    => 'nullable|numeric|min:1|max:100'
            ]);

            $filters = $request->only([
                'status',
                'app_uuid',
                'device_uuid',
                'os',
                'day',
            ]);

            $page    = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);

            $result = $this->subscriptionService->generateEventsReport($page, $perPage, $filters);

            return response()->json($result->toArray());


        } catch (\Throwable $exception) {
            return $this->handleExceptions($exception,
                __('errors.generating_report_failed'),
                'report_error.log',
                $request->all()
            );
        }

    }

    /**
     * @param $clientToken
     * @return JsonResponse
     */
    public function checkSubscription($clientToken): JsonResponse
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

    /**
     * @param Generator $faker
     * @return JsonResponse
     */
    public function subscriptionChanged(Generator $faker): JsonResponse
    {
        $status = $faker->randomElement([200, 400, 500]);

        return response()->json([], $status);
    }
}
