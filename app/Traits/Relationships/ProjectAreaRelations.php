<?php

namespace App\Traits\Relationships;

use App\Models\Project;

trait ProjectAreaRelations
{
   
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
}
