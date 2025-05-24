<?php

namespace App\Traits\Attributes;

trait StageAttributes
{
    
    
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] =  formatDateForDatabase($value);
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = formatDateForDatabase($value);
    }


}
