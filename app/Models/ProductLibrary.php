<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\ProductLibraryRelations;

class ProductLibrary extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        ProductLibraryRelations;

    protected $fillable = [
        'company_id',
        'category_id',
        'supplier_library_id',
        'product_name',
        'description',
        'sku',
        'brand_name',
        'product_url',
        'height',
        'depth',
        'width',
        'length',
        'color',
        'finish',
        'material',
        'notes',
        'retail_price',
        'trade_discount',
        'trade_price',
        'actual_price',
        'trade_terms',
        'production_lead_time',
        'shipping_lead_time',
        'markup',
        'profit',
        'quote_currency_id',
        'exchange_rate',
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
            $model->procurements()->delete();
        });
    }
}
