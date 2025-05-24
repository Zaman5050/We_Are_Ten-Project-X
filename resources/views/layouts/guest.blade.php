<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/icon/favicon.ico') }}" />

    <title>@yield('title', 'Project-X')</title>

    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.2-dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-z.css') }}">

    @stack('styles')
</head>

<body>

    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="sign-up-container">
        <div class="signup-content-container">
            <div class="signup-logo-container">
                <img style="width: 80px; height: 80px;object-fit: contain;" src="{{ asset('assets/images/insite.svg') }}">
            </div>
            <div class="signup-right-container-parent">
                <div class="signup-right-container">
                    @yield('content')
                </div>
            </div>
        </div>
        <img class="sign-up-img" src="{{ asset('assets/images/sign-up.png') }}">
    </div>
    <!-- page container area end -->

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')

</body>

</html>
