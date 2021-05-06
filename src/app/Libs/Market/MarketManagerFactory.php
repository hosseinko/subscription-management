<?php


namespace App\Libs\Market;

/**
 * Class MarketManagerFactory
 * @package App\Libs\Market
 */
class MarketManagerFactory
{

    /**
     * @param $osType
     * @param $username
     * @param $password
     * @return MarketManager
     */
    final public static function make($osType, $username, $password)
    {
        $marketMangerType = ucfirst(config('markets.adapter.type'));

        $class = sprintf('\\%s\\%s\\%sMarketManager', __NAMESPACE__, $marketMangerType, ucfirst($osType));

        return app($class, [
            'username' => $username,
            'password' => $password
        ]);
    }
}
