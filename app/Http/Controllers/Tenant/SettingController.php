<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ServiceInterfaces\Tenant\HomeServiceInterface;

class SettingController extends Controller
{
    private $folder = 'pages.settings.taxes.';
    private $service;

    public function __construct(HomeServiceInterface $homeService)
    {
        $this->service = $homeService;
    }

    public function getTaxes(Request $request)
    {
        $data = $this->service->getDashboardData($request->query());
        return view($this->folder . 'index', compact('data'));
    }
}
