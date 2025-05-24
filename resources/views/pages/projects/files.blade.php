@extends('layouts.tenant.project')

@section('title', 'Project Files')

@section('project-content')

    <project-dropbox-files
        :contentList="{{ json_encode($contentList) }}"
        :members="{{ json_encode($project->members) }}"
        :isDropboxConnected="{{ $isDropboxConnected }}"
    />
    

@endsection