<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Attributes\StageAttributes;
use App\Traits\Relationships\ProjectStageRelations;

class ProjectStage extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        StageAttributes,
        ProjectStageRelations;

    protected $fillable = [
        'project_id',
        'stage_name',
        'amount',
       
        'start_date',
        'due_date',
        'status'
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
