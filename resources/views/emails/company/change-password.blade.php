@extends('emails.base')

@section('title', 'Change Password')

@section('content')

<table style="max-width: 800px;width: 100%; margin:0 auto;border: none" cellspacing="0" cellpadding="0"
    role="presentation">
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px;">Dear <span>{{ $data['company']->name }},</span></td>
    </tr>
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px; line-height: 30px;">

            Your password has been successfully updated by {{ config('app.name') }}. <br> Please use the link below to log in with your new password.
 
        </td>
    </tr>
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px; ">
            <a href="{{ $data['company']->virtual_url }}" style="color: #000; text-decoration: none; cursor: pointer;">
                <u> <b> Login </b> </u>
            </a>
            <b>
            <p> Email: {{ $data['admin']->email }} </p>
            <p> Password: {{ $data['admin']->decryptedPassword }} </p>
        </td>
    </tr>
    <tr>
        <td style="font-size: 18px; padding: 0 65px 28px 65px; line-height: 30px;">
            If you did not request this change or have any concerns, please contact our support team immediately.
        </td>
    </tr>
</table>

@endsection