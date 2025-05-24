<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Attributes\UserLeaveAttributes;
use App\Traits\Relationships\LeaveNegotiationRelations;

class LeaveNegotiation extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        UserLeaveAttributes,
        LeaveNegotiationRelations;

    protected $fillable = [
        'leave_id',
        'user_id',
        'negotiation_type',
        'leave_type',
        'start_date',
        'end_date',
        'number_of_days',
        'notes',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

}
