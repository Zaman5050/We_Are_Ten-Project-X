<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\ProjectAreaRelations;

class ProjectArea extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        ProjectAreaRelations;

    protected $fillable = [
        'project_id',
        'project_uuid',
        'area_name',
        'area_dimention',
        'area_code',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
