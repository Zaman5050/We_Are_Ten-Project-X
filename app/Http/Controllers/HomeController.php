<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\HomeServiceInterface;
class HomeController extends Controller
{
    private $folder = 'pages.super-admin.';
    private $service;

    public function __construct( HomeServiceInterface $homeService)
    {
        $this->service = $homeService;
    }

    public function index()
    {
        $data = $this->service->getDashboardData();
        return view( $this->folder.'dashboard', compact('data') );
    }

}
