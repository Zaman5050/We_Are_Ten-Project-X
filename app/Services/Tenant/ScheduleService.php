<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\ScheduleServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ScheduleRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScheduleExport;
use App\Traits\FileHandler;
use Exception;
use Illuminate\Support\Facades\Storage;

class ScheduleService implements ScheduleServiceInterface
{
    private $repository;
    use FileHandler;

    public function __construct(ScheduleRepositoryInterface $scheduleRepositoryInterface)
    {
        $this->repository = $scheduleRepositoryInterface;
    }

    public function getSchedules(string $projectUuid)
    {
        return $this->repository->getSchedules($projectUuid);
    }

    public function storeSchedule(string $subdomain, string $projectUuid, array $data)
    {
        return $this->repository->storeSchedule($subdomain, $projectUuid, $data);
    }

    public function getMaterials(string $projectUuid)
    {
        return $this->repository->getMaterials($projectUuid);
    }

    public function addScheduleFromLibrary($projectUuid, $data)
    {
        return $this->repository->addScheduleFromLibrary($projectUuid, $data);
    }

    public function exportToExcel(string $projectUuid)
    {
        $schedules = $this->repository->getSchedules($projectUuid);
        return Excel::download(new ScheduleExport($schedules), 'schedules.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function updateSchedule(string $scheduleUuid, array $data)
    {
        return $this->repository->updateSchedule($scheduleUuid, $data);
    }

    public function getAllMaterials()
    {
        return $this->repository->getAllMaterials();
    }

    public function updateMaterial(string $materialUuid, array $data)
    {
        return $this->repository->updateMaterial($materialUuid, $data);
    }

    public function deleteMaterial(string $materialUuid)
    {
        try {
            $this->repository->deleteMaterial($materialUuid);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json(['message' => 'Material has been deleted successfully.'], 200);
    }

    public function storeMaterial(array $data)
    {
        try {
            $this->repository->storeMaterial($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json(['message' => 'Material has been deleted successfully.'], 200);
    }
}
