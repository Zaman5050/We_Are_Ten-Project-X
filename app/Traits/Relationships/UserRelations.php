<?php

namespace App\Traits\Relationships;

use App\Models\Task;
use App\Models\Company;
use App\Models\LeaveNegotiation;
use App\Models\Project;
use App\Models\User;
use App\Models\UserLeave;
use App\Models\Timesheet;

trait UserRelations
{

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function  myTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'project_task_user', 'user_id');
    }

    public function projectOwned()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projectMemberOf()
    {
        return $this->belongsToMany(Project::class);
    }

    public function leaves()
    {
        return $this->hasMany(UserLeave::class);
    }

    public function upcomingLeaves()
    {
        return $this->hasMany(UserLeave::class)->where('start_date', '>', now())->where('status', '=', 'approved');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveNotifications()
    {
        return $this->morphMany('App\Models\Notification', 'notifiable')->orderBy('created_at', 'desc');
    }

    public function leaveNegotiations()
    {
        return $this->hasMany(LeaveNegotiation::class, 'user_id');
    }

    public function upcomingLeave()
    {
        return $this->hasOne(UserLeave::class)->where('start_date', '>', now())->where('status', '=', 'approved');
    }

    public function memberActiveProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
    public function getRemainingLeavesAttribute()
{
    $approvedLeaves = $this->leaves()
        ->where('status', 'approved')
        ->selectRaw('leave_type, SUM(number_of_days) as total_used')
        ->groupBy('leave_type')
        ->pluck('total_used', 'leave_type');

    $pendingLeaves = $this->leaves()
        ->where('status', 'pending')
        ->selectRaw('leave_type, SUM(number_of_days) as total_pending')
        ->groupBy('leave_type')
        ->pluck('total_pending', 'leave_type');

    return [
        'casual' => (int)$this->casual_leaves - (int)($approvedLeaves['casual'] ?? 0),
        'annual' => (int)$this->annual_leaves - (int)($approvedLeaves['annual'] ?? 0),
        'sick' => (int)$this->sick_leaves - (int)($approvedLeaves['sick'] ?? 0),
        'pending' => [
            'casual' => (int)($pendingLeaves['casual'] ?? 0),
            'annual' => (int)($pendingLeaves['annual'] ?? 0),
            'sick' => (int)($pendingLeaves['sick'] ?? 0),
        ],
    ];
}

    
}
