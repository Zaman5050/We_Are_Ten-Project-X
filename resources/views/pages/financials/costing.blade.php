@extends('layouts.tenant.project')

@section('title', 'Financial')

@section('project-content')

    <x-finance.layout>
        <x-finance.costing-tab :costingFinancialData="$costingFinancialData" />
    </x-finance.layout>


@endsection
