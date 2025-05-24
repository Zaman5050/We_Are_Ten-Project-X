<?php

namespace App\Services;

use App\Interfaces\ServiceInterfaces\HomeServiceInterface;
use App\Models\Company;

class HomeService implements HomeServiceInterface
{
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function getDashboardData()
    {
        $data['companies'] = $this->companyService->getAllCompanies();
        $data['active_companies_count'] = Company::whereStatus(Company::STATUS_ACTIVE)->count();
        $data['in_active_companies_count'] = Company::whereStatus(Company::STATUS_IN_ACTIVE)->count();
        return $data;
    }

}
