<?php


namespace App\Libs\Market\Real;


use App\Libs\Market\AbstractMarketManager;

/**
 * Class AbstractRealMarketManager
 * @package App\Libs\Market\Real
 */
abstract class AbstractRealMarketManager extends AbstractMarketManager
{
    /**
     * AbstractRealMarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
    }

    /**
     * @param $receipt
     * @return mixed|void
     */
    public function purchase($receipt)
    {
        // TODO: Implement purchase() method.
    }

    /**
     * @param $receipt
     * @return mixed|void
     */
    public function checkSubscription($receipt)
    {
        // TODO: Implement checkSubscription() method.
    }

    /**
     * @param $action
     * @param $params
     * @return mixed|void
     */
    public function sendRequest($action, $params)
    {
        // TODO: Implement sendRequest() method.
    }

}
