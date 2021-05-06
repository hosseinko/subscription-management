<?php


namespace App\Libs\Market\Mock;

/**
 * Class IosMarketManager
 * @package App\Libs\Market\Mock
 */
class IosMarketManager extends AbstractMockMarketManager
{
    /**
     * IosMarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.ios.mock');
    }
}
