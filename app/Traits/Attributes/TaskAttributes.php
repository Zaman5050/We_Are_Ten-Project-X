<?php

namespace App\Traits\Attributes;

use Carbon\Carbon;

trait TaskAttributes
{

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] =  formatDateForDatabase($value);
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = formatDateForDatabase($value);
    }

    // accessors and modifiers
    public function getStartDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function getDueDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->estimate_time) {
            $totalTimeLogged = $this->timesheet()->sum('total_time');
            $percentage = ($totalTimeLogged / (int) $this->estimate_time) * 100;
    
            return number_format(min($percentage, 100), 2);
        }
    
        return number_format(0, 2);
    }

    public function getTimerModeAttribute($value)
    {
        return $value ?: 'idle';
    }


    public function getTotalTimeAttribute()
    {
        $totalTimeLogged = $this?->timesheet_sum_total_time ?? 0; // Handle null with ?? 0
        $usersCount = $this->users->count();
    
        if ($totalTimeLogged > 0 && $usersCount > 1) {
            $totalTimeLogged /= $usersCount;
        }
    
        return $totalTimeLogged > 0 ? $totalTimeLogged * 60 : 0; // Directly return the value in minutes
    }    

    public function getEstimateTimeFormatedAttribute()
    {
        return $this->estimate_time ? convertFromMinutes($this->estimate_time) : $this->estimate_time;
    }

    public function getMemberEligibleAttribute()
    {
        if($this->timesheet()->count() > 0){
            return (bool) $this->users->filter(function ($user) {
                return $user?->pivot?->eligible_status && $user->id == currentUserId();
            })->count()  > 0;
        }
        return true;
    }
}
