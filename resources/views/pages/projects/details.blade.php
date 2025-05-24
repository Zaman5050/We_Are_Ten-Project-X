@extends('layouts.tenant.project')

@section('title', 'Project Details')

@section('project-content')

<edit-project
    :designers="{{ $designers }}"
    :currencies="{{ $activeCurrencies }}"
    :project="{{ $project }}"
    :check-auth-user="{{ json_encode(auth()->user()->hasRole(auth()->user()::ROLE_ADMIN)) }}"
/>

@endsection