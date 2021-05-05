<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1/'], function () {
    Route::post('register', [SubscriptionController::class, 'register'])
         ->name('register');

    Route::post('purchase', [SubscriptionController::class, 'purchase'])
         ->name('purchase');

    Route::get('subscription/check', [SubscriptionController::class, 'checkSubscription'])
         ->name('check-subscription');

    Route::post('subscription/changed', [SubscriptionController::class, 'subscriptionChanged'])
         ->name('subscription-changed');
});

