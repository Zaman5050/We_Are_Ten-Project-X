@extends('layouts.super-admin')

@section('title', 'Company Register')

@section('content')
    <h2 class="signup-headings">{{ $company->name }}</h2>

    <x-company.update-form :company="$company" />



@endsection
