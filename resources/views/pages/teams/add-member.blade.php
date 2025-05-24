<!-- resources/views/welcome.blade.php -->
@extends('layouts.tenant.index')

@section('title', 'Add Team Member')

@section('content')

    <h2 class="signup-headings">Add team member</h2>
    <add-team :action="{{ json_encode(tenant_route('teams.store')) }}"
        :timezones="{{ json_encode($timezones) }}"></add-team>
@endsection

@push('scripts')
    
@endpush
