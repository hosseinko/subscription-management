<?php


namespace App\Libs\Repositories\Cache;

/**
 * Interface CacheRepository
 * @package App\Libs\Repositories\Cache
 */
interface CacheRepository
{
    /**
     * @param $key
     * @return mixed
     */
    public function exists($key);

    /**
     * @param $key
     * @param $data
     * @param int $ttl
     * @return mixed
     */
    public function store($key, $data, $ttl = 0);

    /**
     * @param $key
     * @return mixed
     */
    public function fetch($key);

    /**
     * @param $key
     * @param $ttl
     * @return mixed
     */
    public function extend($key, $ttl);

    /**
     * @param $key
     * @return mixed
     */
    public function destroy($key);

    /**
     * @param $pattern
     * @return mixed
     */
    public function find($pattern);
}
