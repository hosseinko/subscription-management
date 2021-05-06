<?php


namespace App\Libs\Market;


abstract class AbstractMarketManager implements MarketManager
{
    protected $endpoint;
    protected $username;
    protected $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    abstract public function sendRequest($action, $params);


}
