<?php


namespace App\Libs\Market\Mock;


use App\Enums\MarketActions;
use App\Enums\SubscriptionEvents;
use App\Enums\SubscriptionStatus;
use App\Exceptions\HitRateLimitException;
use App\Exceptions\PurchaseFailedException;
use App\Libs\Market\AbstractMarketManager;
use Carbon\Carbon;
use Http;
use Faker\Generator as Faker;

abstract class AbstractMockMarketManager extends AbstractMarketManager
{
    protected $faker;

    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->faker = app(Faker::class);
    }

    public function purchase($receipt)
    {
        return $this->sendRequest(MarketActions::PURCHASE, ['receipt' => $receipt]);

    }

    public function checkSubscription($receipt)
    {
        return $this->sendRequest(MarketActions::CHECK, ['receipt' => $receipt]);
    }

    public function sendRequest($action, $params)
    {
        if ($action == MarketActions::PURCHASE) {
            $configs = $this->getFakerConfigForPurchase($params);
        } else {
            $configs = $this->getFakerConfigForCheckResponse($params);
        }
        Http::fake($configs);

        $response = Http::withBasicAuth($this->username, $this->password)
                        ->post($this->endpoint . '/' . $action,
                            [
                                'receipt' => $params['receipt']
                            ]);

        switch ($response->status()) {
            case 200:
                return json_decode($response->body(), true);
            case 503:
                throw new PurchaseFailedException(__("errors.purchase_failed"), 503);
            case 429:
                throw new HitRateLimitException(__("errors.too_many_requests"), 429);
        }
    }

    public function getFakerConfigForPurchase($params)
    {
        return [
            config('markets.adapter.urls.android.mock') . '/*' => function () use ($params) {
                $statusCode = $this->purchaseIsValid($params['receipt']) ? 503 : 200;

                if ($statusCode == 200) {
                    return Http::response([
                        'status'      => true,
                        'expire_date' => Carbon::now()->addMonth()->format('Y-m-d H:i:s')
                    ], 200);
                } else {
                    return Http::response(['status' => false], $statusCode);
                }
            },
            config('markets.adapter.urls.ios.mock') . '/*'     => function () use ($params) {
                $statusCode = $this->purchaseIsValid($params['receipt']) ? 503 : 200;

                if ($statusCode == 200) {
                    return Http::response([
                        'status'      => true,
                        'expire_date' => Carbon::now()
                                               ->addMonth()
                                               ->addHours(6)
                                               ->format('Y-m-d H:i:s')
                    ], 200);
                } else {
                    return Http::response(['status' => false], $statusCode);
                }
            }
        ];
    }

    public function getFakerConfigForCheckResponse($params)
    {
        return [
            config('markets.adapter.urls.android.mock') . '/*' => function () use ($params) {
                $statusCode = $this->hitRateLimit($params['receipt']) ? 429 : 200;

                if ($statusCode == 200) {
                    return Http::response([
                        'status' => $this->faker->randomElement(SubscriptionStatus::toArray())
                    ], 200);
                } else {
                    return Http::response(['status' => false], $statusCode);
                }
            },
            config('markets.adapter.urls.ios.mock') . '/*'     => function () use ($params) {
                $statusCode = $this->hitRateLimit($params['receipt']) ? 429 : 200;

                if ($statusCode == 200) {
                    return Http::response([
                        'status'      => true,
                        'expire_date' => Carbon::now()
                                               ->addMonth()
                                               ->addHours(6)
                                               ->format('Y-m-d H:i:s')
                    ], 200);
                } else {
                    return Http::response(['status' => false], $statusCode);
                }
            }
        ];
    }

    public function purchaseIsValid($receipt)
    {
        $receiptLastCharacter = substr($receipt, -1);
        if (is_numeric($receiptLastCharacter) && (int)$receiptLastCharacter % 2 != 0) {
            return true;
        }

        return false;
    }

    public function hitRateLimit($receipt)
    {
        $receiptLastCharacter = substr($receipt, -1);
        if (is_numeric($receiptLastCharacter) && (int)$receiptLastCharacter % 6 == 0) {
            return true;
        }

        return false;
    }

}
