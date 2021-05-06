<?php


namespace App\Libs\Market\Real;


class AndroidMarketManager extends AbstractRealMarketManager
{
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.android.real');
    }


    public function checkSubscription($receipt)
    {
        // TODO: Implement checkSubscription() method.
    }


}
