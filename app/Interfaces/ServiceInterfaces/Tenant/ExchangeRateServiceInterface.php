<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;


interface ExchangeRateServiceInterface
{
    public function getExchangeRates();
    public function storeExchangeRates(array $data);
    public function getExchangeRateLogs();
}
