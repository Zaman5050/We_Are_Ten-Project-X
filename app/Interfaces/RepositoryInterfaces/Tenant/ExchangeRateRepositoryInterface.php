<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;


interface ExchangeRateRepositoryInterface
{
    public function getExchangeRates();
    public function storeExchangeRates(array $data);
    public function getExchangeRateLogs();
}
