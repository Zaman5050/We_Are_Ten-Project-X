<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;


interface ProjectAreaRepositoryInterface
{
 
    public function getProjectAreasAndMeasurementUnit($request);
    public function createOrUpdateProjectAreas($projectUuid, array $data);    
    public function deleteProjectAreas($projectAreaUuid);    
    
}
