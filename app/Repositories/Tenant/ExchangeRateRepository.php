<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\ExchangeRateRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ExchangeRate;
use App\Models\ExchangeRateLog;
use Exception;

class ExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    private $model;

    public function __construct(ExchangeRate $exchangeRateModel)
    {
        $this->model = $exchangeRateModel;
    }

    public function getExchangeRates()
    {
        return $this->model::where('company_id', '=', auth()->user()->company_id)->with('baseCurrency', 'quoteCurrency')->get();
    }

    public function storeExchangeRates(array $data)
    {
        try {
            // Check if exchange rate already exists
            $existingExchangeRate = $this->model::where('base_currency_id', $data['base_currency_id'])
                ->where('quote_currency_id', $data['quote_currency_id'])
                ->where('company_id', auth()->user()->company_id)
                ->first();

            if ($existingExchangeRate) {
                // Update the existing exchange rate
                $existingExchangeRate->update([
                    'exchange_rate' => $data['exchange_rate']
                ]);

                // Log the update in exchange_rate_logs
                $this->logExchangeRateAction($existingExchangeRate, 'update');
                return $existingExchangeRate;
            } else {
                // If exchange rate does not exist, create a new one
                $response = $this->model::create([...$data, 'company_id' => auth()->user()->company_id]);

                // Log the creation in exchange_rate_logs
                $this->logExchangeRateAction($response, 'create');
                return $response;
            }

        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
    private function logExchangeRateAction($exchangeRate, $action)
    {
        // Create a new log entry using the ExchangeRateLog model
        ExchangeRateLog::create([
            'company_id' => auth()->user()->company_id,
            'exchange_rate_id' => $exchangeRate->id ?? '',
            'exchange_rate_id_uuid' => $exchangeRate->uuid ?? '',
            'base_currency_id' => $exchangeRate->base_currency_id ?? '',
            'quote_currency_id' => $exchangeRate->quote_currency_id ?? '',
            'exchange_rate' => $exchangeRate->exchange_rate ?? '',
            // 'action' => $action ?? '',
        ]);
    }
    public function getExchangeRateLogs()
    {
        return ExchangeRateLog::where('company_id', '=', auth()->user()->company_id)
            ->with('baseCurrency', 'quoteCurrency') // Eager load related models
            ->orderBy('created_at', 'desc') // Order logs by creation time
            ->get();
    }

}
