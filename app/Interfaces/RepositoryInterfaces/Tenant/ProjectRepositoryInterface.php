<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;

use App\Models\User;

interface ProjectRepositoryInterface
{
    public function findProject($projectId);
    public function storeProject(array $data);
    public function getAllProjects($query);
    public function getActiveProjects($query,$status);
    public function getCompletedProjects($query);
    public function getDelayedProjects($query);
    public function getArchievedProjects($query);
    public function updateProject(array $data, $uuid);
    public function memberActiveProjects(User $user);
    public function deleteProject(string $uuid);
    public function uploadAttachments($request);
    public function deleteAttachment($attachmentUuid);
    public function getProjectsWithAreasAndMeasurementUnit();
    public function updateDelayedProjects();
    public function getPorjectsByUserId(User $user);
    public function cloneProject(array $data);
    public function updateProjectFinancials(array $data, $uuid);
    public function getProjectBudgetFinancial(string $uuid);
    public function projectWithCostingFinancial(string $uuid);
    public function getProjectServiceFinancial(string $uuid);
    public function projectWithProcurementFinancial(string $uuid);
}
