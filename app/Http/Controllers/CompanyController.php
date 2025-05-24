<?php

namespace App\Http\Controllers;

use App\Interfaces\ServiceInterfaces\CompanyServiceInterface;
use Illuminate\Http\Request;
use App\Models\User;

class CompanyController extends Controller
{
    protected $service;
    protected $folder = 'pages.companies.';

    public function __construct(CompanyServiceInterface $companyService)
    {
        $this->service = $companyService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view($this->folder . 'create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->service->registerCompany($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $company = $this->service->findCompany($uuid);
        $company->load(['admin']);

        return view($this->folder . 'update', ['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        return $this->service->updateCompanyAndAdmin($request, $uuid);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeCompanyAdmin(Request $request)
    {
        return $this->service->storeCompanyAdmin($request);
    }

    public function toggleCompanyStatus($uuid)
    {
        return $this->service->toggleCompanyStatus($uuid);
    }

    public function changePassword(Request $request, User $user)
    {
        return $this->service->changePassword($request, $user);
    }
}
