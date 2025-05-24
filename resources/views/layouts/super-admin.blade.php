<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ 'assets/images/icon/favicon.ico' }}" />

    <title>@yield('title', 'Project-X') - Project-X</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.2-dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/jquery-nice-select-1.1.0/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/default-css.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-z.css') }}" />

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
    @includeIf('includes.super-admin.navbar')
    <div class="page-container">
        <!-- sidebar menu area start -->
        @includeIf('includes.super-admin.sidebar')
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <!-- Main inner content -->
                @yield('content')
                <!-- main content area end -->
            </div>
        </div>
    </div>
</div>
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
    <!-- page container area end -->
    @vite('resources/js/app.js')

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/jquery-nice-select-1.1.0/js/jquery.nice-select.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>


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
