<?php

namespace App\Traits\Relationships;

use App\Models\User;
use App\Models\UserLeave;

trait LeaveNegotiationRelations
{   
    
    public function leave()
    {
        return $this->belongsTo(UserLeave::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
