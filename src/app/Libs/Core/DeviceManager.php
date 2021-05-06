<?php


namespace App\Libs\Core;


use App\Exceptions\ResourceNotFoundException;
use App\Libs\Repositories\Cache\ApplicationsCacheRepository;
use App\Models\Device;

class DeviceManager extends AbstractBaseCore
{
    private $devicesModel;
    private $cacheRepository;

    public function __construct(Device $devicesModel, ApplicationsCacheRepository $applicationsCacheRepository)
    {
        $this->devicesModel    = $devicesModel;
        $this->cacheRepository = $applicationsCacheRepository;
    }

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

    public function getDeviceById($deviceId)
    {
        $device = $this->devicesModel->find($deviceId);
        if (!$device) {
            throw new ResourceNotFoundException(__("errors.device_not_found"), 404);
        }

        return $device;
    }
}
