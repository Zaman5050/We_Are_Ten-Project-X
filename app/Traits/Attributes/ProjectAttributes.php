<?php

namespace App\Traits\Attributes;
use Carbon\Carbon;
use App\Models\Status;


trait ProjectAttributes
{
    
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETE = 'complete';
    const STATUS_DELAYED = 'delayed';
    const STATUS_ARCHIVED = 'archived';
    
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] =  formatDateForDatabase($value);
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = formatDateForDatabase($value);
    }

    public function getShortNameAttribute()
    {
        return collect(explode(' ', $this->name, 2))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->implode('');
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getProjectCompletionPercentageAttribute()
    {
        $totalTaskCount = $this->tasks()->count();
        $completeTaskStatusId = Status::where('name', 'completed')->where('flag','task')->first()?->id;
        $completedTaskCount = $this->tasks()->where('status_id',$completeTaskStatusId)->count();
        if($completeTaskStatusId && $totalTaskCount > 0 && $completedTaskCount > 0)
        {
            return number_format( ($completedTaskCount/$totalTaskCount) * 100,2);
        }
        return number_format( 0,2);
    }

    public function getProjectLoggedTimeAttribute()
    {
        $allTasksEstimatedTimeSum = $this->tasks()->sum('estimate_time');
        $allTasksLoggedTimeSum = $this->timesheet()->sum('total_time');
        $percentage = 0;
        if( isset($allTasksEstimatedTimeSum) && isset($allTasksLoggedTimeSum) && $allTasksEstimatedTimeSum > 0 )
        {
            $percentage = number_format( (int)($allTasksLoggedTimeSum/$allTasksEstimatedTimeSum) * 100,2);
        }
        
        return [
            'timeLogged' => formatSecondsToTimeString($allTasksLoggedTimeSum),
            'percentage' => number_format( (int)$percentage,2),
        ];
    }


}
