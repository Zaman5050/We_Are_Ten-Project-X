<?php

namespace App\Interfaces\RepositoryInterfaces;

use App\Models\Task;
use App\Models\User;


interface TaskRepositoryInterface
{
    public function findTask($uuid);
    public function storeTask($data);
    public function tasksByProject($projectId, $request);
    public function updateTask($taskUuid, $data);
    public function updateTaskData($taskUuid, $data);
    public function tasksStatuses();
    public function updateStatus($taskUuid, $statusUuid);
    public function markFlag($taskUuid, User $user);
    public function markArchived($request);
    public function deleteTask(Task $task);
    public function uploadAttachments($request);
    public function deleteAttachment($attachmentUuid);
    public function updateTimerStatus($request);
    public function getTasksByProjectIdAndUserId($projectId, $userId);
    public function getTasksByUserId($userId);
}
