<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Interfaces\ServiceInterfaces\Tenant\TaskServiceInterface;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Facades\Breadcrumbs;
use App\Models\Project;

class TaskController extends Controller
{
    private $service;

    public function __construct(TaskServiceInterface $taskService) {
        $this->service = $taskService;    
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if($request?->projectUuid && $request->isMethod('GET')){
            $project = Project::findOrFailByUuid($request->projectUuid);
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.show',['project' => $project?->uuid]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Breadcrumbs::add('Tasks', 'task.index',['projectUuid' => $request->projectUuid]);
        $data = $this->service->getListing($request);
        return view('pages.tasks.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        return $this->service->storeTask($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        return $this->service->getTask($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request)
    {
        return $this->service->updateTask($request);
    }

    public function updateTaskData(Request $request)
{
        return $this->service->updateTaskData($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($subdomain, Task $id)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        return $this->service->updateStatus($request);
    }

    public function markFlag(Request $request)
    {
        $currentUser = currentUser();
        return $this->service->markFlag($request, $currentUser);
    }

    public function markArchived(Request $request)
    {
        return $this->service->markArchived($request);
    }

    public function deleteTask(Request $request)
    {
        return $this->service->deleteTask($request);
    }

    public function upload(Request $request)
    {
        return $this->service->uploadAttachments($request);
    }

    public function deleteAttachment(Request $request)
    {
        return $this->service->deleteAttachment($request);
    }

}
