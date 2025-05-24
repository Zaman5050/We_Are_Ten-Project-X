<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\ExchangeRateLogRelations;


class ExchangeRateLog extends Model
{
    use HasFactory, HasUUID, SoftDeletes,ExchangeRateLogRelations;

    protected $fillable = [
        'uuid',
        'company_id',
        'exchange_rate_id',
        'exchange_rate_id_uuid',
        'base_currency_id',
        'quote_currency_id',
        'exchange_rate',
        'action',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
