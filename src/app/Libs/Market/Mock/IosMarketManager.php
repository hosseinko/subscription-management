<?php


namespace App\Libs\Market\Mock;


class IosMarketManager extends AbstractMockMarketManager
{
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.ios.mock');
    }

    public function checkSubscription($receipt)
    {
        // TODO: Implement checkSubscription() method.
    }
}
