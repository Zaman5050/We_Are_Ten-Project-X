<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\ExchangeRateServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ExchangeRateRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    private $repository;

    public function __construct(ExchangeRateRepositoryInterface $exchangeRaterepository)
    {
        $this->repository = $exchangeRaterepository;
    }

    public function getExchangeRates()
    {
        try {
            return $this->repository->getExchangeRates();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function storeExchangeRates(array $data)
    {
        try {
            $exchangeRateResponse = $this->repository->storeExchangeRates($data);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'exchange_rate' => $exchangeRateResponse->load('baseCurrency', 'quoteCurrency'),
            'message' => 'Exchange rate is added successfully.',
        ], 201);
    }
    public function getExchangeRateLogs()
    {
        try {
            return $this->repository->getExchangeRateLogs();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
