@extends('layouts.tenant.index')

@section('title', 'Profile')

@section('content')
<!-- Main inner content -->
<div class="d-flex justify-content-between mb-3">
    <h1 class="dashboard-main-heading mb-0">Profile Details</h1>
    <delete-modal
        :description="'Are you sure you want to remove this member?'"
        :modal_id="'member-delete-modal'"
        :url="{{ json_encode( tenant_route('member.profile.delete', [ 'uuid' => request()?->uuid ]) ) }}"
        ref="deleteModal">
        <button
            class="filter-btn mb-2 float-end bg-yellow text-decoration-underline"
            @click="$refs.deleteModal.openModal()">
            Remove Team Member
        </button>
    </delete-modal>
</div>

<profile :timezones="{{ json_encode($timezones) }}" :member="{{ json_encode($member) }}"
    role="{{ auth()->user()->role_name }}" is_member_profile />
<!-- main content area end -->
@endsection