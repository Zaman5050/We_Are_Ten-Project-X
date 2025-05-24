<?php

namespace App\Services;

use App\Interfaces\ServiceInterfaces\Tenant\TimesheetServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\TimesheetRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\TaskRepositoryInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use Illuminate\Support\Arr;

class TimesheetService implements TimesheetServiceInterface
{
    protected $repository;
    protected $taskTepository;
    protected $projectService;

    public function __construct(
        TimesheetRepositoryInterface $TimesheetRepositoryInterface,
        TaskRepositoryInterface $TaskRepositoryInterface,
        ProjectServiceInterface $ProjectServiceInterface
    ) {
        $this->repository = $TimesheetRepositoryInterface;
        $this->taskTepository = $TaskRepositoryInterface;
        $this->projectService = $ProjectServiceInterface;
    }
    public function listing($request)
    {
        $user = currentUser();
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 20);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        // Fetch paginated timesheets
        $timesheets = $this->repository->getTimesheets($user, $page, $perPage, $startDate, $endDate);

        // Fetch member projects and list of members
        $memberProjects = $this->projectService->memberActiveProjects($user);
        $memberProjects = Arr::select($memberProjects->unique()->toArray(), ['uuid', 'name', 'tasks']);
        $memberProjects = collect($memberProjects);

        $members = Arr::collapse(Arr::pluck($timesheets->toArray(), 'task.users'));
        $memberList = collect($members)->unique('id');

        // Check if the request expects JSON
        if ($request->expectsJson()) {
            return response()->json([
                'timesheets' => $timesheets->items(), // Only the data, not the paginator
                'memberProjects' => $memberProjects,
                'memberList' => $memberList,
                'pagination' => [
                    'currentPage' => $timesheets->currentPage(),
                    'lastPage' => $timesheets->lastPage(),
                    'total' => $timesheets->total(),
                ]
            ]);
        }

        return view('pages.timesheets.index', compact('timesheets', 'memberProjects', 'memberList'));
    }



    public function timeLogged($request)
    {
        $request->validate([
            "taskUuid" => ['required', 'string', 'exists:project_tasks,uuid'],
            "end_time" => ['nullable'],
            "start_time" => ['nullable'],
            "total_time" => ['nullable'],
            "timer_mode" => ['nullable', 'string', 'in:idle,active,paused'],
        ]);

        $task = $this->taskTepository->findTask($request->taskUuid);
        return $this->repository->timeLogged($request, $task);
    }


    public function updateTimeLogged($request)
    {
        $request->validate([
            "taskUuid" => ['required', 'string', 'exists:project_tasks,uuid'],
            "timesheet_uuids" => ['required', 'array', 'exists:timesheets,uuid'],
            "end_time" => ['nullable'],
            "start_time" => ['nullable'],
            "total_time" => ['nullable'],
            "timer_mode" => ['nullable', 'string', 'in:idle,active,paused'],
        ]);
        $task = $this->taskTepository->findTask($request->taskUuid);
        return $this->repository->updateTimeLogged($request, $task);
    }
    public function updateTimeLoggedForReassigned($request, $task)
    {
        return $this->repository->updateTimeLogged($request, $task);
    }

    public function findTimesheet($uuid)
    {
        return $this->repository->findTimesheet($uuid);
    }

    public function updateTimerStatus($request)
    {
        $request->validate([
            "taskUuid" => ['required', 'string', 'exists:project_tasks,uuid'],
            "timer_mode" => ['required', 'string', 'in:idle,active,paused'],
        ]);

        $task = $this->taskTepository->updateTimerStatus($request);
        return response()->json(['message' => 'Timer status updated successfully'], 200);
    }

    public function deleteTimeEntry($request)
    {

        $status = $this->repository->deleteTimeEntry($request);

        if ($status) {
            session()->flash('message',  'Time entry deleted successfully');
            return response()->json(['error' => false], 200);
        }
        return response()->json(['error' => true], 200);
    }

    public function addEntryTime($data)
    {

        $task = $this->taskTepository->findTask($data['task_uuid']);
        return $this->repository->addTimeEntry($data, $task);
    }
    public function getTimeSheetData($userId, $taskId)
    {
        return $this->repository->getTimeSheetData($userId, $taskId);
    }
}
