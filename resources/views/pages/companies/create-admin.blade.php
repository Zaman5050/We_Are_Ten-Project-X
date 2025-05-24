@extends('layouts.super-admin')

@section('title', 'Admin Register')

@section('content')

    <h2 class="signup-headings">Create Account</h2>
    
    <x-admin.form
        :company_uuid="request()->company_uuid"
    />
    
@endsection
