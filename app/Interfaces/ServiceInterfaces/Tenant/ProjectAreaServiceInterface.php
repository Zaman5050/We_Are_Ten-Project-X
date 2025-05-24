<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;


interface ProjectAreaServiceInterface
{
    public function getProjectAreasAndMeasurementUnit($projectUuid);
    public function createOrUpdateProjectAreas($projectUuid, array $data);
    public function deleteProjectAreas($projectAreaUuid);    
    
   
}
