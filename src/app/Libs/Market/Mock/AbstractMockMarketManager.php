<?php


namespace App\Libs\Market\Mock;


use App\Enums\MarketActions;
use App\Exceptions\HitRateLimitException;
use App\Exceptions\PurchaseFailedException;
use App\Libs\Market\AbstractMarketManager;
use Carbon\Carbon;
use Http;

abstract class AbstractMockMarketManager extends AbstractMarketManager
{
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
    }

    public function purchase($receipt)
    {
        return $this->sendRequest(MarketActions::PURCHASE, ['receipt' => $receipt]);

    }

    public function sendRequest($action, $params)
    {
        Http::fake([
            config('markets.adapter.urls.android.mock') . '/*' => function () use ($params) {
                $statusCode = $this->checkResponse($params['receipt']);

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
                $statusCode = $this->checkResponse($params['receipt']);

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
        ]);

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

    public function checkResponse($receipt)
    {
        if (!$this->purchaseIsValid($receipt)) {
            return 503;
        }

        if ($this->hitRateLimit($receipt)) {
            return 429;
        }

        return 200;
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
