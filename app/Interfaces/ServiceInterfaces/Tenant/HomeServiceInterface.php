<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface HomeServiceInterface
{
    
    public function getDashboardData($query);

}