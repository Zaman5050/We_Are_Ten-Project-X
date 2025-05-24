<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Status;
use Carbon\Carbon;

class TaskObserver
{
    /**
     * Handle the Task "saved" event (covers both created and updated).
     */
    public function saved(Task $task)
    {
        if ($task->isDirty('status_id')) {
            $this->updateProjectStatus($task);
        }
    }
    

    /**
     * Update the project's status based on task statuses.
     */
    private function updateProjectStatus(Task $task)
    {
        $project = $task->project;
        if (!$project) {
            return;
        }

        $completedStatusId = Status::where('name', 'completed')->value('id');

        $dueDate = $project->due_date ? Carbon::parse($project->due_date)->endOfDay() : null;
        $isOverdue = $dueDate ? Carbon::now()->gt($dueDate) : false;

        $allTasksCompleted = $project->tasks->every(fn($task) => $task->status_id == $completedStatusId);
        $newStatus = $allTasksCompleted ? 'completed' : ($isOverdue ? 'delayed' : 'active');

        if ($project->status !== $newStatus) {
            $project->status = $newStatus;
            $project->save();
        }
    }
}
