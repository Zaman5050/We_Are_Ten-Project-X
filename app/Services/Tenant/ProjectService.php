<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ProjectRepositoryInterface;
use Exception;
use App\Models\User;
use App\Interfaces\RepositoryInterfaces\TaskRepositoryInterface;
use Carbon\Carbon;

class ProjectService implements ProjectServiceInterface
{
    private $repository,
        $taskRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository, TaskRepositoryInterface $taskRepository)
    {
        $this->repository = $projectRepository;
        $this->taskRepository = $taskRepository;
    }

    public function findProject($projectId)
    {
        return $this->repository->findProject($projectId);
    }

    public function getAllProjects($query = [])
    {
        return $this->repository->getAllProjects($query);
    }

    public function storeProject(array $data)
    {
        return $this->repository->storeProject($data);
    }

    public function getActiveProjects($query = [],$status='active')
    {
        return $this->repository->getActiveProjects($query,$status);
    }

    public function getCompletedProjects($query = [])
    {
        return $this->repository->getCompletedProjects($query);
    }

    public function getDelayedProjects($query = [])
    {
        return $this->repository->getDelayedProjects($query);
    }

    public function getArchievedProjects($query = [])
    {
        return $this->repository->getArchievedProjects($query);
    }

    public function getProjectDashboardData($uuid)
    {
        $data['project'] = $this->repository->findProject($uuid)->load(['tasks.users', 'members', 'owner', 'tags', 'stages', 'membersOnLeave']);
        $data['myTasks'] = $this->taskRepository->getTasksByProjectIdAndUserId($data['project']['id'], auth()->id());
        return $data;
    }

    public function getProjectTeam($uuid)
    {
        return $this->repository->findProject($uuid)->load(['members.upcomingLeaves']);
    }

    public function updateProject(array $data, $uuid)
    {
        return $this->repository->updateProject($data, $uuid);
    }


    public function memberActiveProjects(User $user)
    {
        return $this->repository->memberActiveProjects($user);
    }

    public function deleteProject(string $uuid)
    {
        try {
            $this->repository->deleteProject($uuid);
        } catch (Exception $e) {
            throw $e;
        }
        return redirect(tenant_route('projects.index'))->with(['message' => 'Project has been deleted successfully.'], 200);
    }

    public function uploadAttachments($request)
    {
        $request->validate([
            "attachments" => [
                "required",
                "array",
                "max:200360", // "mimes:jpeg,jpg,png,gif,svg"
            ],
            "sub_path" => ["required", "string"]
        ]);

        $imageIds = null;

        if ($request->has('attachments')) {
            $imageIds = $this->repository->uploadAttachments($request);
        }

        return response()->json(['attachments' => $imageIds]);
    }

    public function deleteAttachment($request)
    {
        $attachmentUuid = $request->attachmentUuid;
        return $this->repository->deleteAttachment($attachmentUuid);
    }

    public function getProjectsWithAreasAndMeasurementUnit()
    {
        return $this->repository->getProjectsWithAreasAndMeasurementUnit();
    }

    public function updateDelayedProjects()
    {
        return $this->repository->updateDelayedProjects();
    }
    public function cloneProject($request)
    {
        $project = $this->repository->findProject($request)->load(['owner', 'tags', 'stages', 'areas', 'timesheet', 'attachment', 'attachments']);
        $clonedProjectData = $project->toArray();
        unset($clonedProjectData['id'], $clonedProjectData['uuid'], $clonedProjectData['created_at'], $clonedProjectData['updated_at'], $clonedProjectData['owner_id'], $clonedProjectData['company_id']);

        $clonedProjectData['name'] = $clonedProjectData['name'] . ' (Replica)';
        $clonedProjectData['start_date'] = Carbon::parse($clonedProjectData['start_date'])->format('d/m/y');
        $clonedProjectData['due_date'] = Carbon::parse($clonedProjectData['due_date'])->format('d/m/y');

        $attachmentUuids = array_column($clonedProjectData['attachments'], 'uuid');
        $clonedProjectData['attachments'] = $attachmentUuids;

        return $this->repository->cloneProject($clonedProjectData);
    }

    public function getProjectBudgetFinancial(string $uuid)
    {
        return $this->repository->getProjectBudgetFinancial($uuid);
    }

    public function updateProjectFinancials($data, $uuid)
    {
        return $this->repository->updateProjectFinancials($data, $uuid);
    }

    public function projectWithCostingFinancial(string $uuid)
    {
        return $this->repository->projectWithCostingFinancial($uuid);
    }

    public function getProjectServiceFinancial(string $uuid)
    {
        return $this->repository->getProjectServiceFinancial($uuid);
    }

    public function projectWithProcurementFinancial(string $uuid)
    {
        return $this->repository->projectWithProcurementFinancial($uuid);
    }
}
