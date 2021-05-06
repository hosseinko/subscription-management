<?php


namespace App\Libs\Market\Mock;


use App\Enums\MarketActions;

class AndroidMarketManager extends AbstractMockMarketManager
{
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.android.mock');
    }

    public function checkSubscription($receipt)
    {
        // TODO: Implement checkSubscription() method.
    }
}
