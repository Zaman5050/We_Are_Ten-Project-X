<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\MaterialLibraryRelations;

class MaterialLibrary extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        MaterialLibraryRelations;

    protected $fillable = [
        'company_id',
        'supplier_library_id',
        'category_id',
        'item_name',
        'description',
        'sku',
        'brand_name',
        'doc_code',
        'product_url',
        'height',
        'depth',
        'width',
        'length',
        'color',
        'finish',
        'price',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

    protected static function booted()
    {
        static::deleted(function ($model) {
            // Code to be executed when the model is deleted
            $model->cover_image()->delete();
            $model->specifications()->delete();
            $model->attachments()->delete();
            $model->schedules()->delete();
        });
    }
}
