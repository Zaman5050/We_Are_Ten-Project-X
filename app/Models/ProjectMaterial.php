<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\ProjectMaterialRelations;

class ProjectMaterial extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        ProjectMaterialRelations;

    protected $fillable = [
        'company_id',
        'project_id',
        'material_library_id',
        'quantity',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
