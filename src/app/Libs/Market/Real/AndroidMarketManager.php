<?php


namespace App\Libs\Market\Real;

/**
 * Class AndroidMarketManager
 * @package App\Libs\Market\Real
 */
class AndroidMarketManager extends AbstractRealMarketManager
{
    /**
     * AndroidMarketManager constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->endpoint = config('markets.adapter.urls.android.real');
    }
}
