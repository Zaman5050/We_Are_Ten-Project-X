@extends('layouts.tenant.project')

@section('title', 'Chats')

@section('project-content')

 <chat :project="{{$project}}" :projects="{{ json_encode($projects) }}" :chats="{{json_encode($chats)}}" :members="{{ $teamMembers }}"></chat>

@endsection
