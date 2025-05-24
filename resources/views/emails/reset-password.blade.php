@extends('emails.base')

@section('title', 'Reset Password')

@section('content')

    <table style="max-width: 800px;width: 100%; margin:0 auto;border: none" cellspacing="0" cellpadding="0" role="presentation">
        
        <tr>
            <td style="font-size: 24px; font-weight: 700; padding: 0 65px 28px 65px;"> <b>Forgot Password?</b> </td>
        </tr>
        <tr>
            <td style="font-size: 18px; font-weight: 700; padding: 0 65px 28px 65px;">Dear {{ $user->full_name }}!</td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px;">
                We understand that you've forgotten your password for your {{ config('app.name') }} account. We're here to help you regain access.
            </td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px; line-height: 30px;">
                To reset your password, please follow these steps:
                <ol>
                    <li>Click on the link below or copy and paste it into your browser: <br>
                        <a  href="{{ @$resetLink }}" style="color: #000; text-decoration: none; cursor: pointer;"><u>Reset Password Link</u></a>
                    </li>
                    <li>
                        Follow the instructions on the page to create a new password.
                    </li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px; ">
                If you did not request a password reset please feel free to ignore this email. If you need any further assitance, feel free to contact our <b><u>Support Team</u></b>.
            </td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px; ">
                <br><br>
                Thank you for being a valued member of our community.
            </td>
        </tr>
        
    </table>

@endsection