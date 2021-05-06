<?php


namespace App\Libs\Market\Mock;


use App\Enums\MarketActions;

/**
 * Class AndroidMarketManager
 * @package App\Libs\Market\Mock
 */
class AndroidMarketManager extends AbstractMockMarketManager
{
    /**
     * AndroidMarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.android.mock');
    }
}
