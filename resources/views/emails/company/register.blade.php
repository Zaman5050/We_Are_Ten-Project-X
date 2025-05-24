@extends('emails.base')

@section('title', 'Registration Link')

@section('content')

<table style="max-width: 800px;width: 100%; margin:0 auto;border: none" cellspacing="0" cellpadding="0"
    role="presentation">
    <tr>
        <td style="font-size: 18px; font-weight: 700; padding: 0 65px 28px 65px;">
            Welcome to {{ config('app.name') }}!
        </td>
    </tr>
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px;">Dear <span>{{ $data['company']->name }},</span></td>
    </tr>
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px; line-height: 30px;">
            Thank you for choosing {{ config('app.name') }}. We are excited to have you on board and look forward to
            providing you with the best service possible. <br>
            To sign in please use the domain link and details below.
        </td>
    </tr>
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px; ">
            <a href="{{ $data['company']->virtual_url }}" style="color: #000; text-decoration: none; cursor: pointer;">
                <u> <b> Login </b> </u>
            </a>
            <b>
            <p> Company Url: <a href="{{ $data['company']->virtual_url }}" style="color: #000; text-decoration: none; cursor: pointer;">
                <u> <b> {{ $data['company']->virtual_url }}</b> </u>
            </a> </p>
            <p> Email: {{ $data['admin']->email }} </p>
            <p> Password: {{ $data['admin']->decryptedPassword }} </p>
        </td>
    </tr>
</table>

@endsection