<?php

namespace App\Traits\Relationships;

use App\Models\Currency;

trait ExchangeRateRelations
{
    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function quoteCurrency()
    {
        return $this->belongsTo(Currency::class, 'quote_currency_id');
    }
}
