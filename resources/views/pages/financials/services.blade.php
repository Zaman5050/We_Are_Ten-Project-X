@extends('layouts.tenant.project')

@section('title', 'Financial')

@section('project-content')

    <x-finance.layout>
        <x-finance.services-tab :serivceFinancialData="$serivceFinancialData" />
    </x-finance.layout>

@endsection
