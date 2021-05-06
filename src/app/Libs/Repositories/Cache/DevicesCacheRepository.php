<?php


namespace App\Libs\Repositories\Cache;


interface DevicesCacheRepository
{
    public function exists($key);

    public function store($key, $data, $ttl = 0);

    public function fetch($key);

    public function extend($key, $ttl);

    public function destroy($key);

    public function find($pattern);
}
