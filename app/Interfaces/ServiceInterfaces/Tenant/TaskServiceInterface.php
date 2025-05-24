<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;

use App\Models\Task;
use App\Models\User;
use App\Models\Status;
use Illuminate\Support\Facades\Request;

interface TaskServiceInterface
{
    public function findTask($uuid);
    public function getTask($request);
    public function getListing($request);
    public function storeTask($request);
    public function tasksByProject($projectUuid, $request);
    public function updateTask($request);
    public function updateTaskData($request);
    public function tasksStatuses();
    public function updateStatus($request);
    public function markFlag($request, User $user);
    public function markArchived($request);
    public function deleteTask($request);
    public function uploadAttachments($request);
    public function deleteAttachment($request);
}