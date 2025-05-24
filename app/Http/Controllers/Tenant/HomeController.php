<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ServiceInterfaces\Tenant\HomeServiceInterface;

class HomeController extends Controller
{
    private $folder = 'pages.tenant.';
    private $service;

    public function __construct( HomeServiceInterface $homeService)
    {
        $this->service = $homeService;
    }

    public function index(Request $request)
    {
        $data = $this->service->getDashboardData($request->query());
        return view( $this->folder.'dashboard', compact('data') );
    }

}
