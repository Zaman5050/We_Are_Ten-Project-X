<?php

namespace App\Traits\Relationships;

use App\Models\ExchangeRate;

trait CurrencyRelations
{
    public function baseCurrencyExchangeRates()
    {
        return $this->hasMany(ExchangeRate::class, 'base_currency_id')->where('company_id', '=', auth()->user()->company_id);
    }

    public function quoteCurrencyExchangeRates()
    {
        return $this->hasMany(ExchangeRate::class, 'quote_currency_id')->where('company_id', '=', auth()->user()->company_id);
    }
}
