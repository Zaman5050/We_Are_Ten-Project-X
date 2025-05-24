<?php

namespace App\Traits\Relationships;

use App\Models\LeaveNegotiation;
use App\Models\User;

trait UserLeaveRelations
{
     // Relationships
     public function user()
     {
         return $this->belongsTo(User::class, 'user_id');
     }
     public static function findOrFailByUuid(string $uuid)
     {
         return static::byUUID($uuid)->firstOrFail();
     }
     public function leaveNegotiations()
     {
         return $this->hasMany(LeaveNegotiation::class, 'leave_id');
     }

}
