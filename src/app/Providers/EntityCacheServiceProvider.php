<?php

namespace App\Providers;

use App\Libs\Repositories\Cache\CacheRepository;
use App\Libs\Repositories\Cache\DevicesCacheRepository;
use App\Libs\Repositories\Cache\Redis\RedisCacheRepository;
use App\Libs\Repositories\Cache\Redis\RedisDevicesCacheRepository;
use Illuminate\Support\ServiceProvider;

class EntityCacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CacheRepository::class, RedisCacheRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
