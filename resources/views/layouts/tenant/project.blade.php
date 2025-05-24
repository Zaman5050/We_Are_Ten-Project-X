@extends('layouts.tenant.index')

@section('content')
    <!-- Main inner content -->
    <div class="sales-report-area">

        <div class="dashboard-heading-container pb--40 d-flex justify-content-between">
            <h1 class="dashboard-main-heading mb-0">{{ getProjectName(request()->project ?? request()->projectUuid) }}</h1>
        </div>

        @include('includes.project.project-navbar')

        @yield('project-content')

    </div>
    <!-- main content area end -->
@endsection



@push('scripts')
    <script>
        $(document).ready(function() {

            $(".selector").flatpickr({
                mode: "range",
                dateFormat: "m-d-Y",
            });
            $(".selector-single").flatpickr({
                // mode: "range",
                dateFormat: "m-d-Y",
            });


        })
    </script>
@endpush
