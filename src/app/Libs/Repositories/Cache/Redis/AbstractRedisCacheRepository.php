<?php


namespace App\Libs\Repositories\Cache\Redis;


abstract class AbstractRedisCacheRepository
{
    protected $redisConnection;

    public function __construct()
    {
        $this->redisConnection = app('redis')->connection('entity_cache');
    }

    public function exists($key)
    {

        return $this->redisConnection->has($key);
    }

    public function store($key, $data, $ttl = 0)
    {
        try {
            $this->redisConnection->set($key, $data);

            if ($ttl > 0) {
                $this->redisConnection->expire($key, $ttl);
            }

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function fetch($key)
    {
        $data = $this->redisConnection->get($key);
        if (!$data) {
            return false;
        }

        return $data;
    }

    public function extend($key, $ttl)
    {
        return $this->redisConnection->expire($key, $ttl);
    }

    public function destroy($key)
    {
        return $this->redisConnection->expire($key, 0);
    }

    public function find($pattern)
    {
        return $this->redisConnection->keys($pattern);
    }
}
