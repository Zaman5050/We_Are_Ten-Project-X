@extends('layouts.tenant.project')

@section('title', 'Project Areas')

@section('project-content')


<project-area 
    :project-areas="{{ json_encode($data['projectAreas']) }}" 
    :measurement-unit="{{ json_encode($data['measurementUnit']) }}"
    :project-uuid="{{ json_encode( request()->project ) }}"
/>

@endsection