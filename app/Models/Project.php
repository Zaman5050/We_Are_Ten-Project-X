<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Relationships\ProjectRelations;
use App\Traits\Attributes\ProjectAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;


class Project extends Model
{
    use HasFactory,
        HasUUID,
        ProjectAttributes,
        ProjectRelations,
        SoftDeletes;

    protected $table = 'projects';


    protected $fillable = [
        'name',
        'code',
        'address',
        'longitude',
        'latitude',
        'measurement_unit',
        'type',
        'is_procurement_enable',
        'start_date',
        'due_date',
        'owner_id',
        'description',
        'company_id',
        'currency_id',
        'status',
    ];
    protected $casts = [];

    protected $dates = ['start_date', 'end_date'];

    protected $appends = [
        'project_completion_percentage',
        'project_logged_time',
        'short_name'
    ];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
    public static function findOrFailById(string $id)
    {
        return static::whereId($id)->firstOrFail();
    }

    public static function boot()
    {
        parent::boot();
        
        static::deleting(function ($project) {
            // Delete related timesheets through tasks first
            foreach ($project->tasks as $task) {
                $task->timesheets()->delete();
            }

            // Then delete related records in order
            $project->tasks()->delete();
            $project->tags()->delete();
            $project->stages()->delete();
            $project->chats()->delete();
            $project->members()->detach();
        });
    }

    public static function updateDelayedProjects()
    {
        self::whereNotIn('status', ['delayed', 'completed'])
            ->where('due_date', '<', now())
            ->update(['status' => 'delayed']);
    }
}
