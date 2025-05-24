@extends('layouts.tenant.index')

@section('title', 'Teams')

@section('content')
<!-- Main inner content -->

<x-team.layout :member="$member" :activeProjectCount="$projectCount">
    @if ($member->remaining_leaves)

    <div class="dashboard-table-container for-shadow br-6 mb-4">
        <table class="table mb-0 dashboard-table">
            <thead>
                <tr>
                    <th>Remaining Casual Leave</th>
                    <th>Remaining Sick Leave</th>
                    <th>Remaining Annual Leave</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td>{{ $member->remaining_leaves['casual'] }}</td>
                        <td>{{ $member->remaining_leaves['sick'] }}</td>
                        <td>{{ $member->remaining_leaves['annual'] }}</td>
                    </tr>
            </tbody>
        </table>
    </div>
@endif
    <div class="d-flex justify-content-between align-items-center pb--10" id="pending-leave-request-shadow">
        <h6 class="table-date-heading">
            Pending Requests
            <span>({{ $pendingLeaves->count() }})</span>
        </h6>
        <div class="collaps-container">
            <button class="collaps-expand-btn" data-target="#pending-leave-request" data-Shadow="#pending-leave-request-shadow">
                Collapse
            </button>
            <img class="collaps-icon" src="{{ asset('assets/images/arrow-icon.svg') }}">
        </div>
    </div>
    <div style="padding-left: 1px;padding-right:1px;" class="container-for-table">
        <pending-leaves-table
            :leaves="{{ $pendingLeaves }}" 
            :member='@json($member)'
            />
    </div>
    <div class="d-flex justify-content-between align-items-center pb--10" id="leave-history-shadow">
        <h6 class="table-date-heading">
            History
            <span>({{ $historyLeaves->count() }})</span>
        </h6>
        <div class="collaps-container">
            <button
                class="collaps-expand-btn"
                data-target="#leave-history"
                data-Shadow="#leave-history-shadow">
                Collapse
            </button>
            <img
                class="collaps-icon"
                src="{{ asset('assets/images/arrow-icon.svg') }}">
        </div>
    </div>
    <div style="padding-left: 1px; max-width: 851px; padding-right:1px" class="container-for-table">
        <x-team.member-history-leaves-table :leaves="$historyLeaves" />
    </div>
</x-team.layout>
@if($leaves->isNotEmpty())
@include('popups.apply-leave')
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        let collapsButton = document.querySelectorAll('.collaps-expand-btn');
        collapsButton.forEach((item) => {


            item.addEventListener('click', function(e) {
                let img = e.target.nextElementSibling
                let button = this;
                let dataId = item.getAttribute('data-target');
                let element = document.querySelector(dataId);
                let dataShadow = item.getAttribute('data-Shadow');
                let icon = document.querySelector('.collaps-icon');
                let shadowElement = document.querySelector(dataShadow);

                if (button.textContent === 'Expand') {

                    element.classList.remove('custom-collapsed');

                    button.textContent = 'Collapse';
                    img.style.transform = 'rotate(180deg)'
                    shadowElement.classList.remove('shadow-container')

                } else {

                    element.classList.add('custom-collapsed');
                    button.textContent = 'Expand';
                    img.style.transform = 'rotate(0deg)'
                    shadowElement.classList.add('shadow-container')

                }
            });

        })

    })
</script>


@endpush
