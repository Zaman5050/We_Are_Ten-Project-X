<?php

namespace App\Interfaces\ServiceInterfaces;

use App\Models\User;
use Illuminate\Support\Facades\Request;

interface CompanyServiceInterface
{
    public function registerCompany($requestData);
    public function storeCompanyAdmin($data);
    public function getAllCompanies();
    public function findCompany($uuid);
    public function toggleCompanyStatus(string $uuid);
    public function changePassword($request, User $user);
    public function updateCompanyAndAdmin($request, $uuid);

}
