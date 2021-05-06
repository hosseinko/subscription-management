<?php


namespace App\Libs\Core;


use App\Exceptions\ResourceNotFoundException;
use App\Libs\Repositories\Cache\CacheRepository;
use App\Models\Application;

/**
 * Class ApplicationManager
 * @package App\Libs\Core
 */
class ApplicationManager extends AbstractBaseCore
{
    private $applicationModel;
    private $cacheRepository;

    /**
     * ApplicationManager constructor.
     * @param Application $applicationModel
     * @param CacheRepository $applicationsCacheRepository
     */
    public function __construct(Application $applicationModel, CacheRepository $applicationsCacheRepository)
    {
        $this->applicationModel = $applicationModel;
        $this->cacheRepository  = $applicationsCacheRepository;
    }

    /**
     * @param $uuid
     * @return mixed
     * @throws ResourceNotFoundException
     */
    public function getApplicationByUuid($uuid)
    {
        $cacheKey = "application:$uuid";

        $application = $this->cacheRepository->fetch($cacheKey);

        if ($application) {
            return json_decode($application);
        }

        $application = $this->applicationModel->getApplicationByUuid($uuid);

        $this->cacheRepository->store($cacheKey, json_encode($application->toArray()));

        return $application;
    }

    /**
     * @param $applicationId
     * @return mixed
     * @throws ResourceNotFoundException
     */
    public function getApplicationById($applicationId)
    {
        $application = $this->applicationModel->find($applicationId);
        if (!$application) {
            throw new ResourceNotFoundException(__("errors.application_not_found"), 404);
        }

        return $application;
    }
}
