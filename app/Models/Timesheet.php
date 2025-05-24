<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\User;

class Timesheet extends Model
{
    use HasFactory, HasUUID, SoftDeletes;

    protected $fillable = [
        'project_task_id',
        'user_id',
        'is_automatic',
        'start_time',
        'end_time',
        'total_time'
    ];
    protected $casts = [];

    protected $appends = ['created_date', 'start_time_formated', 'end_time_formated'];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
 
    public function getStartTimeFormatedAttribute($value)
    {
        return Carbon::parse($this->start_time)->format('h:i a');
    }

    public function getEndTimeFormatedAttribute($value)
    {
        return Carbon::parse($this->end_time)->format('h:i a');
    }

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($this->start_time)->format('d/m/Y');
    }

    public function task()
    {
        return $this->hasOne( Task::class, 'id', 'project_task_id' );
    }

    public function tasks()
    {
        return $this->hasMany( Task::class, 'id', 'project_task_id' );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
