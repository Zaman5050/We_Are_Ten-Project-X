<?php

namespace App\Services;

use App\Http\Resources\Tenant\TeamResource;
use App\Http\Resources\StatusResource;
use App\Interfaces\RepositoryInterfaces\TaskRepositoryInterface;
use App\Interfaces\ServiceInterfaces\Tenant\TaskServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\TimesheetServiceInterface;
use App\Http\Resources\Tenant\TaskResource;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use Illuminate\Support\Carbon;
use Exception;

class TaskService implements TaskServiceInterface
{
    protected $repository;
    protected $projectService;
    protected $timeSheetService;

    public function __construct(
        TaskRepositoryInterface $TaskRepositoryInterface,
        ProjectServiceInterface $ProjectServiceInterface,
        TimesheetServiceInterface $timesheetServiceInterface
    ) {
        $this->repository = $TaskRepositoryInterface;
        $this->projectService = $ProjectServiceInterface;
        $this->timeSheetService = $timesheetServiceInterface;
    }

    public function getListing($request)
    {
        $currentAssignee = [];
        if ($request->query('assignee')) {
            $currentAssignee = explode(',', $request->query('assignee'));
        }

        $searchQuery = $request->query('search', '');
        $projectId = $request->projectUuid;
        $activeTab = $request->segment(3);

        $project = $this->projectService->findProject($projectId);
        $tasks = $this->tasksByProject($project?->id, $request->all());
        $tasks = $tasks->sortKeys();

        $tasks = TaskResource::collection($tasks)->response()->getData(true);
        $project->setRelation("tasks", $tasks);

        $taskStatuses = $this->tasksStatuses();
        $taskStatuses = StatusResource::collection($taskStatuses)->response()->getData(true);

        $stages = $project->stages->toArray();

        $teamMembers = collect();
        if ($project?->members) {
            $teamMembers = TeamResource::collection($project?->members->unique())->response()->getData(true) ?: collect();
        }

        return [
            'project' => $project,
            'activeTab' => $activeTab,
            'taskStatuses' => $taskStatuses,
            'stages' => $stages,
            'teamMembers' => $teamMembers,
            'projectId' => $projectId,
            'currentAssignee' => $currentAssignee,
            'searchQuery' => $searchQuery,
        ];
    }

    public function getTask($request)
    {
        $task = $this->findTask($request->taskUuid);
        if ($request->query('format') == 'json') {
            $task->load(['owner', 'status', 'attachments', 'stage'])
                ->withSum('timesheet', 'total_time');
            return TaskResource::make($task);
        }
        return $task;
    }

    public function findTask($uuid)
    {
        return $this->repository->findTask($uuid);
    }

    public function storeTask($request)
    {
        return $this->repository->storeTask($request);
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

        $imageIds = [];

        if ($request->has('attachments')) {
            $imageIds = $this->repository->uploadAttachments($request);
        }

        return response()->json(['attachments' => $imageIds]);
    }

    public function updateTask($request)
    {
        $taskUuid = $request->taskUuid;
        return $this->repository->updateTask($taskUuid, $request->validated());
    }
    public function updateTaskData($request)
    {
        $taskUuid = $request->taskUuid;
        $task = $this->repository->findTask($taskUuid);
        if ($task->timer_mode == 'active') {
            $members = $task->users->pluck('id')->toArray();
            $authUser = auth()->user();
            // If the user is not an admin and is not the previous member, deny access
            if ($authUser->role_name !== 'admin' && $request->previousMemberUuid != $authUser->uuid) {
                return response()->json([
                    'error' => true,
                    'message' => 'You are not allowed to change the assigned member!',
                ], 403);
            }
            $timeSheetData = $this->timeSheetService->getTimeSheetData($members[0], $task->id);

            $startTime = Carbon::parse($timeSheetData->start_time)->tz(config('app.timezone'));
            $end_time = $request->end_time ? $request->end_time : Carbon::now();
            $endTime = Carbon::parse($end_time)->tz(config('app.timezone'));
            $timeDiffInSeconds = $endTime->diffInSeconds($startTime);
            $updateData = [
                'taskUuid' => $taskUuid,
                'timesheet_uuids' => [$timeSheetData->uuid], // assuming $timeSheetUuid is a valid UUID
                'timer_mode' => 'paused',
                'total_time' => $timeDiffInSeconds,
                'end_time' => $endTime->toDateTimeString(),
            ];
            $updateDataCollection =  collect($updateData);
            $this->timeSheetService->updateTimeLoggedForReassigned($updateDataCollection, $task);
        }
        return $this->repository->updateTaskData($taskUuid, $request->input());
    }

    public function tasksByProject($projectId, $request)
    {
        return $this->repository->tasksByProject($projectId, $request);
    }

    public function tasksStatuses()
    {
        return $this->repository->tasksStatuses();
    }

    public function updateStatus($request)
    {
        $taskUuid = $request->taskUuid;
        $statusUuid = $request->statusUuid;
        return $this->repository->updateStatus($taskUuid, $statusUuid);
    }

    public function markFlag($request, $user)
    {

        $taskUuid = $request->taskUuid;
        $updatedTask = $this->repository->markFlag($taskUuid, $user);

        return response()->json([
            "status" => !is_null($updatedTask->flaged_by)
        ]);
    }

    public function markArchived($request)
    {
        return $this->repository->markArchived($request);
    }

    public function deleteTask($request)
    {
        $taskUuid = $request->taskUuid;
        $task = $this->repository->findTask($taskUuid);
        $this->repository->deleteTask($task);

        return redirect()->back()->with([
            "message" => $task->task_code . " Task deleted seccesfully"
        ]);
    }

    public function deleteAttachment($request)
    {
        $attachmentUuid = $request->attachmentUuid;
        return $this->repository->deleteAttachment($attachmentUuid);
    }
}
