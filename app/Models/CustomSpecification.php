<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\CustomSpecificationRelations;

class CustomSpecification extends Model
{
    use HasFactory,
    HasUUID,
    SoftDeletes,
    CustomSpecificationRelations;

    protected $fillable = [
        'specificationable_id',
        'specificationable_type',
        'label',
        'custom_description',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

}
