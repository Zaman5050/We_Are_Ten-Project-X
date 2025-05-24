<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class CompanyRegisteredEvent
{
    use Dispatchable;

    public $company, $admin;

    public function __construct($company, $admin)
    {
        $this->company = $company;
        $this->admin = $admin;
    }
}
