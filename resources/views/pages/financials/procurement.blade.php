@extends('layouts.tenant.project')

@section('title', 'Financial')

@section('project-content')

    <x-finance.layout>
        <x-finance.procurement-tab :procurementFinancialData="$procurementFinancialData" />
    </x-finance.layout>

@endsection
