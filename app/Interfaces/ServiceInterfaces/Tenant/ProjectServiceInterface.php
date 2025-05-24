<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;

use App\Models\User;
use Illuminate\Support\Facades\Request;

interface ProjectServiceInterface
{
    public function findProject($projectId);
    public function storeProject(array $data);
    public function getAllProjects($query);
    public function getActiveProjects($query,$status);
    public function getCompletedProjects($query);
    public function getDelayedProjects($query);
    public function getArchievedProjects($query);
    public function getProjectDashboardData($uuid);
    public function getProjectTeam($uuid);
    public function updateProject(array $data, $uuid);
    public function memberActiveProjects(User $user);
    public function deleteProject(string $uuid);
    public function uploadAttachments($request);
    public function deleteAttachment($attachmentUuid);
    public function getProjectsWithAreasAndMeasurementUnit();
    public function cloneProject($request);
    public function getProjectBudgetFinancial(string $uuid);
    public function updateProjectFinancials(array $data, $uuid);
    public function projectWithCostingFinancial(string $uuid);
    public function getProjectServiceFinancial(string $uuid);
    public function projectWithProcurementFinancial(string $uuid);
}
