@extends('layouts.tenant.project')

@section('title', 'Financial')

@section('project-content')

    <x-finance.layout>
        <financial :project="{{ json_encode($project) }}" :categories="{{ json_encode($categories) }}" />
    </x-finance.layout>

@endsection
