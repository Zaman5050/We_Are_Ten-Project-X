<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\ProcurementRelations;
use App\Traits\Attributes\ProcurementAttributes;

class Procurement extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        ProcurementRelations,
        ProcurementAttributes;

    protected $fillable = [
        'company_id',
        'project_id',
        'product_library_id',
        'project_area_id',
        'quantity',
        'order_date',
        'quote_currency_id',
        'base_currency_id',
        'exchange_rate',
        'retail_price',
        'trade_discount',
        'trade_price',
        'budget',
        'actual_price',
        'trade_terms',
        'production_lead_time',
        'shipping_lead_time',
        'order_date',
        'shipping_date',
        'delivery_date',
        'markup',
        'total_exclusive_tax',
        'total_inclusive_tax',
        'profit',
        'status',
    ];
    protected $casts = [];
    const STATUSES = [
        'Draft',
        'Internal Review',
        'Client Review',
        'Closed',
        'Rejected',
        'Approved',
        'Ordered',
        'Payment Due',
        'Shipped',
        'Installed',
        'Delivered',
    ];
    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
