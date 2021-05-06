<?php

namespace App\Providers;

use App\Libs\Repositories\Cache\ApplicationsCacheRepository;
use App\Libs\Repositories\Cache\DevicesCacheRepository;
use App\Libs\Repositories\Cache\Redis\RedisApplicationCacheRepository;
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
        $this->app->bind(ApplicationsCacheRepository::class, RedisApplicationCacheRepository::class);
        $this->app->bind(DevicesCacheRepository::class, RedisDevicesCacheRepository::class);
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
