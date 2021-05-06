<?php


namespace App\Libs\Market;

/**
 * Interface MarketManager
 * @package App\Libs\Market
 */
interface MarketManager
{

    /**
     * MarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password);

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function setCredentials($username, $password);

    /**
     * @param $receipt
     * @return mixed
     */
    public function purchase($receipt);

    /**
     * @param $receipt
     * @return mixed
     */
    public function checkSubscription($receipt);

    /**
     * @param $action
     * @param $params
     * @return mixed
     */
    public function sendRequest($action, $params);

}
