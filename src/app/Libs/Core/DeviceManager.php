<?php


namespace App\Libs\Core;


use App\Exceptions\ResourceNotFoundException;
use App\Libs\Repositories\Cache\CacheRepository;
use App\Models\Device;

/**
 * Class DeviceManager
 * @package App\Libs\Core
 */
class DeviceManager extends AbstractBaseCore
{
    private $devicesModel;
    private $cacheRepository;

    /**
     * DeviceManager constructor.
     * @param Device $devicesModel
     * @param CacheRepository $applicationsCacheRepository
     */
    public function __construct(Device $devicesModel, CacheRepository $applicationsCacheRepository)
    {
        $this->devicesModel    = $devicesModel;
        $this->cacheRepository = $applicationsCacheRepository;
    }

    /**
     * @param $deviceUuid
     * @param $os
     * @param $lang
     * @return mixed
     */
    public function firstOrCreateDevice($deviceUuid, $os, $lang)
    {
        $cacheKey = "device:$deviceUuid";

        $device = $this->cacheRepository->fetch($cacheKey);
        if ($device) {
            return json_decode($device);
        }

        $device = $this->devicesModel->firstOrCreate(
            ['uuid' => $deviceUuid],
            [
                'os'   => $os,
                'lang' => $lang
            ]
        );

        $this->cacheRepository->store($cacheKey, json_encode($device->toArray()));

        return $device;
    }

    /**
     * @param $deviceId
     * @return mixed
     * @throws ResourceNotFoundException
     */
    public function getDeviceById($deviceId)
    {
        $device = $this->devicesModel->find($deviceId);
        if (!$device) {
            throw new ResourceNotFoundException(__("errors.device_not_found"), 404);
        }

        return $device;
    }
}
