<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\HomeServiceInterface;
use App\Services\Tenant\ProjectService;
use App\Services\Tenant\UserLeaveService;
use App\Models\Procurement;

class HomeService implements HomeServiceInterface
{
    private $projectService;
    private $userLeaveService;

    public function __construct(ProjectService $projectService,UserLeaveService $userLeaveService)
    {
        $this->projectService = $projectService;
        $this->userLeaveService = $userLeaveService;
    }

    public function getDashboardData($query)
    {
        $projects = $this->projectService->getAllProjects($query);
        $leaves = $this->userLeaveService->getAllUserLeavesToday();
        $projects->load('currency');
        $data['projects'] = $projects;
        $data['leaves'] = $leaves;
        $data['projectsCount'] = count($this->projectService->getAllProjects());
        $data['activeProjectCount'] = count($this->projectService->getActiveProjects($query));
        $data['completedProjectCount'] = count($this->projectService->getCompletedProjects($query));
        $data['delayedProjectCount'] = count($this->projectService->getDelayedProjects($query));
        $data['archivedProjectCount'] = count($this->projectService->getArchievedProjects($query));
        $data['totalProfit'] = Procurement::whereIn('project_id', $this->projectService->getActiveProjects()->pluck('id'))->sum('profit');
        return $data;
    }

}
