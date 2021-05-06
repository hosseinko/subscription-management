<?php


namespace App\Libs\Core;


use App\Exceptions\ResourceNotFoundException;
use App\Libs\Repositories\Cache\ApplicationsCacheRepository;
use App\Models\Application;

class ApplicationManager extends AbstractBaseCore
{
    private $applicationModel;
    private $cacheRepository;

    public function __construct(Application $applicationModel, ApplicationsCacheRepository $applicationsCacheRepository)
    {
        $this->applicationModel = $applicationModel;
        $this->cacheRepository  = $applicationsCacheRepository;
    }

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

    public function getApplicationById($applicationId)
    {
        $application = $this->applicationModel->find($applicationId);
        if (!$application) {
            throw new ResourceNotFoundException(__("errors.application_not_found"), 404);
        }

        return $application;
    }

}
