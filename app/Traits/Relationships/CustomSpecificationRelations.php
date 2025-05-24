<?php

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait CustomSpecificationRelations
{
    
    public function specificationable(): MorphTo
    {
        return $this->morphTo();
    }

}
