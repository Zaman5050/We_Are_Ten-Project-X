@extends('emails.base')

@section('title', 'Reset Password')

@section('content')

    <table style="max-width: 800px;width: 100%; margin:0 auto;border: none" cellspacing="0" cellpadding="0" role="presentation">
        
        <tr>
            <td style="font-size: 24px; font-weight: 700; padding: 0 65px 28px 65px;"> <b>Account Setup</b> </td>
        </tr>
        <tr>
            <td style="font-size: 18px; font-weight: 700; padding: 0 65px 28px 65px;">Dear {{ $member->full_name }}!</td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px;">
                You have been invited by {{ $adminName }} as a Team Member 
            </td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px; line-height: 30px;">
                To create your password, please follow these steps:
                <ol>
                    <li>Click on the link below or copy and paste it into your browser: <br>
                        <a  href="{{ @$createPasswordLink }}" style="color: #000; text-decoration: none; cursor: pointer;"><u>Account Setup Link</u></a>
                    </li>
                    <li>
                        Follow the instructions on the page to create and confirm password.
                    </li>
                    <li>
                        Add email address and password to log into the account.
                    </li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-size: 18px; padding: 0 65px 28px 65px; ">
                For any further assitance, feel free to contact our <b><u>Support Team</u></b>.
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