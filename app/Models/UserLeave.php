<?php

namespace App\Models;

use App\Traits\Relationships\UserRelations;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Attributes\UserLeaveAttributes;

class UserLeave extends Model
{
    use HasFactory,
    HasUUID,
    SoftDeletes,
    UserRelations,
    UserLeaveAttributes;

    protected $table = 'user_leaves';

    // Primary key if different from convention
    protected $primaryKey = 'id';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'number_of_days',
        'notes',
        'status',
    ];

    // Cast attributes to specific types
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
    ];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

}
