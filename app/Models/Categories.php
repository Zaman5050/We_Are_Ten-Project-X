<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Relationships\CategoryRelations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use HasFactory, HasUUID, SoftDeletes, CategoryRelations;

    protected $fillable = [
        'company_id',
        'name',
        'type',
    ];
    protected $casts = [];
    const TYPE_PRODUCT = 'product';
    const TYPE_MATERIAL = 'material';

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
