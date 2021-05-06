<?php


namespace App\Libs\Market\Real;


use App\Libs\Market\AbstractMarketManager;

abstract class AbstractRealMarketManager extends AbstractMarketManager
{
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
    }

    public function purchase($receipt)
    {
        // TODO: Implement purchase() method.
    }

    public function sendRequest($action, $params)
    {
        // TODO: Implement sendRequest() method.
    }

}
