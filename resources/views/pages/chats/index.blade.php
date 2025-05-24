@extends('layouts.tenant.index')

@section('title', 'Chats')

@section('content')
    <!-- Main inner content -->
    <div class="sales-report-area">
        <div class="dashboard-table-container">
            <chat :projects="{{ json_encode($projects) }}" :direct="{{json_encode($directChats)}}" :chats="{{json_encode($chats)}}" :members="{{ $teamMembers }}" type="company"></chat>
        </div>

    </div>
    <!-- main content area end -->
@endsection

@push('scripts')
 
@endpush('scripts')