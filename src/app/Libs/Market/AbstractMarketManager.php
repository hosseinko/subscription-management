<?php


namespace App\Libs\Market;

/**
 * Class AbstractMarketManager
 * @package App\Libs\Market
 */
abstract class AbstractMarketManager implements MarketManager
{
    protected $endpoint;
    protected $username;
    protected $password;

    /**
     * AbstractMarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param $username
     * @param $password
     * @return mixed|void
     */
    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param $action
     * @param $params
     * @return mixed
     */
    abstract public function sendRequest($action, $params);
}
