@extends('layouts.super-admin')

@section('title', 'Dashboard')

@section('content')
<!-- Main inner content -->
<div class="sales-report-area">
    <div class="dashboard-heading-container">
        <h1 class="dashboard-main-heading">Dashboard</h1>
    </div>
    <div class="pb--20">
        <div class="row">
            <x-dashboard.card title="Total Companies" :count="count($data['companies'])" />
            <x-dashboard.card title="Active companies" :count="$data['active_companies_count']" />
            <x-dashboard.card title="Deactivated companies" :count="$data['in_active_companies_count']" />
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="dashboard-button-container mb-3">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="active summary-finalcial px-2" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Companies</button>
                </li>
                <li class="nav-item" role="presentation" style="display: none;">
                    <button class="summary-finalcial px-2" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Project Types</button>
                </li>
            </ul>
        </div>
        <a class="filter-btn bg-yellow" href="{{ route('super-admin.companies.create') }}">Add Company</a>
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab"></div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab"></div>
    </div>

    <div class="d-flex justify-content-between align-items-center pb--10">
        <h6 class="total-projects">Companies <span>({{ count($data['companies']) }})</span></h6>
        <div class="collaps-container">
            <button class="collaps-expand-btn" data-target="#super-dashboard-table" > Collapse</button>
            <img class="collaps-icon" src="{{ asset('assets/images/arrow-icon.svg') }}">
        </div>
    </div>
    <div class="dashboard-table-container">

        <x-company.company-table :companies="$data['companies']" />
    </div>
</div>
<!-- main content area end -->

@includeIf('popups.change-password', [ 'adminUuid' => 'null' ])
@includeIf('popups.company-toggle-status', [ 'companyUuid' => 'null' ])

@endsection


@push('scripts')
<script src="/assets/pages/js/dashboard.js"></script>
<script>
    $(document).ready(function() {

        let collapsButton = document.querySelectorAll('.collaps-expand-btn');
        collapsButton.forEach((item) => {


            item.addEventListener('click', function(e) {
                let img = e.target.nextElementSibling
            let button = this;
            let dataId = item.getAttribute('data-target');
            let element =document.querySelector(dataId);
            let dataShadow = item.getAttribute('data-Shadow');
            let icon = document.querySelector('.collaps-icon');
            let shadowElement =document.querySelector(dataShadow);

            if (button.textContent === 'Expand') {
                        
                        element.classList.remove('custom-collapsed');
                        
                        button.textContent = 'Collapse';
                        img.style.transform='rotate(180deg)'
                       shadowElement.classList.remove('shadow-container')

                    } else {
                      
                        element.classList.add('custom-collapsed');
                        button.textContent = 'Expand';
                        img.style.transform='rotate(0deg)'
                       shadowElement.classList.add('shadow-container')

                    }
                });

        })


    })
</script>
@endpush
