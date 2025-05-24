@extends('layouts.tenant.index')

@section('title', 'Product Library')

@section('content')


    <product-library :products="{{ json_encode($allCompanyProducts) }}" :suppliers="{{ json_encode($suppliers) }}"
        :projects="{{ json_encode($projects) }}" :categories="{{ json_encode($categories) }}"
        :currencies="{{ json_encode($activeCurrencies) }}" />

@endsection
