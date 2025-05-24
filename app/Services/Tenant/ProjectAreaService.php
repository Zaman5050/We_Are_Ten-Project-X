<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\ProjectAreaServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ProjectAreaRepositoryInterface;

class ProjectAreaService implements ProjectAreaServiceInterface
{
    private $repository;

    public function __construct(ProjectAreaRepositoryInterface $projectAreaRepository)
    {
        $this->repository = $projectAreaRepository;
    }

    public function getProjectAreasAndMeasurementUnit($projectUuid)
    {
        return $this->repository->getProjectAreasAndMeasurementUnit($projectUuid);   
    }

    public function createOrUpdateProjectAreas($projectUuid, array $data)
    {
        return $this->repository->createOrUpdateProjectAreas($projectUuid, $data);
    }

    public function deleteProjectAreas($projectAreaUuid)
    {
        return $this->repository->deleteProjectAreas($projectAreaUuid);
    }



}
