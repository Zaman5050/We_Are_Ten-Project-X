<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
    <title>@yield('title', 'Project-X') - Project-X</title>
    @stack('styles')

</head>

<body style=" margin: 0; font-family:  'Inter', sans-serif;">

    <table style="max-width: 800px;width: 100%; margin:0 auto;border: none" cellspacing="0" cellpadding="0"
        role="presentation">

        <tr>
            <td style="padding: 58.21px 64px 96.84px 64px;"><img src="{{ asset('assets/images/nav-default-img.png') }}"></td>
        </tr>
    </table>

    @yield('content')

    <table style="max-width: 800px;width: 100%; margin:0 auto;border: none" cellspacing="0" cellpadding="0"
        role="presentation">

        <tr>
            <td style="font-size: 18px; padding: 0 65px 108px 65px;line-height: 30px;">Best Regards,
                <br>{{ config('app.name') }}
            </td>
        </tr>
        <tr>
            <td>
                <hr style="max-width:533px;width:100%;margin: 0 auto;">
            </td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 12px; font-weight: 500; color: #919191;padding: 30.5px 0;">
                &copy;
                {{ date('Y'); }}
                {{ config('app.name') }}, Inc. All rights reserved.</td>
        </tr>
    </table>

    @stack('scripts')

</body>

</html>
