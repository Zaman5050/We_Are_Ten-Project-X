<?php


namespace App\Traits\Attributes;

use Carbon\Carbon;

trait UserLeaveAttributes
{
    public function getStartDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getEndDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] =  formatDateForDatabase($value);
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = formatDateForDatabase($value);
    }
 
}
