@extends('layouts.tenant.index')

@section('title', 'Teams')

@section('content')
<!-- Main inner content -->


<x-team.layout :member="$member" :activeProjectCount="count($projects)">
    <x-team.member-projects-table :projects="$projects" />
</x-team.layout>


@endsection
