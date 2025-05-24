@extends('layouts.tenant.index')

@section('title', 'Exchange Rates | Settings')

@section('content')

    <exchange-rate :currencies="{{ json_encode($activeCurrencies) }}" :exchange_rates="{{ json_encode($exchangeRates) }}" :exchange_rate_logs="{{ json_encode($exchangeRatesLogs) }}" />

@endsection
