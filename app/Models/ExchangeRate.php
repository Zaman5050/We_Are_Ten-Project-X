<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\ExchangeRateRelations;

class ExchangeRate extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        ExchangeRateRelations;

    protected $fillable = [
        'company_id',
        'base_currency_id',
        'quote_currency_id',
        'exchange_rate',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
