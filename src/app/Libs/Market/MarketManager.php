<?php


namespace App\Libs\Market;


interface MarketManager
{

    public function __construct($username, $password);

    public function purchase($receipt);

    public function checkSubscription($receipt);

    public function sendRequest($action, $params);

}
