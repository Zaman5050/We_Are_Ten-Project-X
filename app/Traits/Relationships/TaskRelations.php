<?php

namespace App\Traits\Relationships;

use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\TaskAttachment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProjectStage;
use App\Models\Timesheet;

trait TaskRelations
{

    // relationship
    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_task_user', 'project_task_id')->withPivot('eligible_status');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class, 'project_task_id');
    }

    public function stage()
    {
        return $this->hasOne(ProjectStage::class, 'id', 'stage_id');
    }

    public function timesheet()
    {
        return $this->hasMany(Timesheet::class, 'project_task_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }
}
