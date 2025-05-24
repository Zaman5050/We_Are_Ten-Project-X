<?php

namespace App\Traits\Relationships;

use App\Models\ExchangeRate;
use App\Models\Currency;

trait ExchangeRateLogRelations
{
    // Relationship with ExchangeRate (You should define the relationship in the ExchangeRate model as well)
    public function exchangeRate()
    {
        return $this->belongsTo(ExchangeRate::class, 'exchange_rate_id');
    }

    // Relationship with Base Currency (via the ExchangeRate model)
    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    // Relationship with Quote Currency (via the ExchangeRate model)
    public function quoteCurrency()
    {
        return $this->belongsTo(Currency::class, 'quote_currency_id');
    }
}
