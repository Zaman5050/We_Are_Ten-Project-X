<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface ScheduleRepositoryInterface
{
    public function getSchedules(string $projectUuid);
    public function storeSchedule(string $subdomain, string $projectUuid, array $data);
    public function getMaterials(string $projectUuid);
    public function addScheduleFromLibrary( string $projectUuid, array $data);
    public function updateSchedule(string $scheduleUuid, array $data);
    public function getAllMaterials();
    public function updateMaterial(string $materialUuid, array $data);
    public function deleteMaterial(string $materialUuid);
    public function storeMaterial(array $data);
}
