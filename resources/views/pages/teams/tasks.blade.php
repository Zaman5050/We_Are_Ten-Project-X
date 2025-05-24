@extends('layouts.tenant.index')

@section('title', 'Teams')

@section('content')
    <!-- Main inner content -->


    <x-team.layout :member="$member" :activeProjectCount="$projectCount">
        <x-team.member-tasks-table :tasks="$tasks" />
    </x-team.layout>


@endsection
