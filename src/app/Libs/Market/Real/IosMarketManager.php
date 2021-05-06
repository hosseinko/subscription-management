<?php


namespace App\Libs\Market\Real;

/**
 * Class IosMarketManager
 * @package App\Libs\Market\Real
 */
class IosMarketManager extends AbstractRealMarketManager
{
    /**
     * IosMarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.ios.real');
    }
}
