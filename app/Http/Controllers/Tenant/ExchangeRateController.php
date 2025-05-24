<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ServiceInterfaces\Tenant\ExchangeRateServiceInterface;
use App\Http\Requests\ExchangeRateRequest;

class ExchangeRateController extends Controller
{
    private $folder = 'pages.settings.exchange-rate.';
    private $service;

    public function __construct(ExchangeRateServiceInterface $exchangeRateService)
    {
        $this->service = $exchangeRateService;
    }

    public function index(Request $request)
    {
        $exchangeRates = $this->service->getExchangeRates();
        $exchangeRatesLogs = $this->service->getExchangeRateLogs();
        return view($this->folder . 'index', compact('exchangeRates','exchangeRatesLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExchangeRateRequest $request)
    {
        return $this->service->storeExchangeRates($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        // 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request) {}
}
