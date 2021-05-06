<?php


namespace App\Libs\Repositories\Cache\Redis;


use App\Libs\Repositories\Cache\CacheRepository;

/**
 * Class RedisCacheRepository
 * @package App\Libs\Repositories\Cache\Redis
 */
class RedisCacheRepository implements CacheRepository
{
    protected $redisConnection;

    /**
     * RedisCacheRepository constructor.
     */
    public function __construct()
    {
        $this->redisConnection = app('redis')->connection('entity_cache');
    }

    /**
     * @param $key
     * @return mixed
     */
    public function exists($key)
    {

        return $this->redisConnection->has($key);
    }

    /**
     * @param $key
     * @param $data
     * @param int $ttl
     * @return bool
     */
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

    /**
     * @param $key
     * @return false
     */
    public function fetch($key)
    {
        $data = $this->redisConnection->get($key);
        if (!$data) {
            return false;
        }

        return $data;
    }

    /**
     * @param $key
     * @param $ttl
     * @return mixed
     */
    public function extend($key, $ttl)
    {
        return $this->redisConnection->expire($key, $ttl);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function destroy($key)
    {
        return $this->redisConnection->expire($key, 0);
    }

    /**
     * @param $pattern
     * @return mixed
     */
    public function find($pattern)
    {
        return $this->redisConnection->keys($pattern);
    }
}
