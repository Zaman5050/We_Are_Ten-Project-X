<?php

namespace App\Traits\Attributes;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

trait UserAttributes
{

    public function getProfilePictureAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name ?? '';
    }

    public function getRoleNameAttribute()
    {
        return $this->getRoleNames()->first() ?? null;
    }

    public function getShortNameAttribute()
    {
        return collect(explode(' ', $this->full_name, 2))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->implode('');
    }

    public function getTotalTimeLoggedAttribute()
    {
        return  (float) $this->timesheets?->sum('total_time') ;
    }
    public function setLeavingDateAttribute($value)
    {
        $this->attributes['leaving_date'] =  formatDateForDatabase($value);
    }
    public function setJoiningDateAttribute($value)
    {
        $this->attributes['joining_date'] =  formatDateForDatabase($value);
    }
    public function getLeavingDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : '';
    }
    public function getJoiningDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : '';
    }
}
