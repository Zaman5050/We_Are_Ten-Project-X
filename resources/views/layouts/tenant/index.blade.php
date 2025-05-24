<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ 'assets/images/icon/favicon.ico' }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-uuid" content="{{ auth()->user()->uuid }}">
    <title>@yield('title', 'Project-X') - Project-X</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.2-dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/jquery-nice-select-1.1.0/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/default-css.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-z.css') }}" />
    @vite('resources/css/app.css')
    @stack('styles')

</head>

<body>
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <div id="app">

        <!-- preloader area end -->
        <!-- page container area start -->
        @includeIf('includes.tenant.navbar', [
            "notifications" => @$notifications
        ])
        <div class="page-container">
            {{-- @dd($activetasks) --}}
            <!-- sidebar menu area start -->
            @includeIf('includes.tenant.sidebar')
            @if(!empty($activetasks))
                <log-timer-block 
                :task="{{ json_encode($activetasks) }}"
                :user-id="{{ auth()->user()->id }}" 
                ></log-timer-block>
            @endif
        
            <!-- sidebar menu area end -->
            <!-- main content area start -->
            <div class="main-content">
                <div class="main-content-inner">
                    @includeIf('popups.apply-leave')
                    <!-- Main inner content -->
                    @yield('content')
                    <!-- main content area end -->
                </div>
            </div>
        </div>
        <!-- page container area end -->

    </div>
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/jquery-nice-select-1.1.0/js/jquery.nice-select.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}&loading=async&libraries=places"></script>
    <script>
        window.currentAuth = {
            authUuid: "{{ auth()?->user()?->uuid }}" || null,
            authName: "{{ auth()?->user()?->full_name }}" || "",
        };
                
        const APP_ENV = {
            VITE_PUSHER_APP_KEY: "{{env('VITE_PUSHER_APP_KEY')}}",
            VITE_PUSHER_APP_CLUSTER: "{{env('VITE_PUSHER_APP_CLUSTER')}}",
            VITE_PUSHER_HOST: "{{env('VITE_PUSHER_HOST')}}",
            VITE_PUSHER_PORT: "{{env('VITE_PUSHER_PORT')}}",
            VITE_PUSHER_SCHEME: "{{env('VITE_PUSHER_SCHEME')}}",
        }        
    </script>

    @vite('resources/js/app.js')

    <script>
        const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        const BASE_URL = "{{ url('/') }}";


        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            
            const $toggleCollapse = $('.toggle-collapse');
            $toggleCollapse.on('click', function(e) {
                const $target = $(e.target);
                const $nextElement = $target.next();
                
                if ($target.text() === 'Expand') {
                    $nextElement.css('transform', 'rotate(0deg)');
                } else {
                    $nextElement.css('transform', 'rotate(180deg)');
                }
            });

        });


    </script>
    <script src="/assets/pages/js/dashboard.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if (session('message') && session('alert-type'))
                const toastType = "{{ session('alert-type') }}";
                const message = "{{ session('message') }}";
    
                // Wait for Vue to initialize
                window.Toast = window.Toast || null;
                if (window.Toast) {
                    window.Toast[toastType](message, {
                        timeout: 3000,
                        position: "top-right",
                    });
                } else {
                    console.error("Vue Toast is not initialized.");
                }
            @endif
        });
    </script>
    @stack('scripts')

</body>

</html>
