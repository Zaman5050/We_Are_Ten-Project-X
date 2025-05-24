@extends('layouts.tenant.index')

@section('title', 'Profile')

@section('content')
    <!-- Main inner content -->

    <h1 class="dashboard-main-heading">Profile Details</h1>

    <profile :timezones="{{ json_encode($timezones) }}" :member="{{ json_encode($member) }}" role="{{auth()->user()->role_name}}" />
    <!-- main content area end -->
@endsection