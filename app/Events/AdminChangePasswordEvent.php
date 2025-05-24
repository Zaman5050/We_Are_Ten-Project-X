<?php

namespace App\Events;

use Illuminate\Support\Facades\Mail;
use App\Mail\AdminChangePasswordMail;
use Illuminate\Foundation\Events\Dispatchable;

class AdminChangePasswordEvent
{
    use Dispatchable;

    public $company, $admin;

    public function __construct($company, $admin)
    {
        $data['company'] = $company;
        $data['admin'] = $admin;

        Mail::to($company->email)->send(new AdminChangePasswordMail($data));
    }
}
