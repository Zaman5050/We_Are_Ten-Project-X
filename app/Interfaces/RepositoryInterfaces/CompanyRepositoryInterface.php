<?php

namespace App\Interfaces\RepositoryInterfaces;

use App\Models\User;

interface CompanyRepositoryInterface
{
    public function register($requestData);
    public function findCompany($uuid);
    public function getAllCompanies();
    public function storeCompanyAdmin( array $data );
    public function toggleCompanyStatus( string $data );
    public function changePassword($request, User $user);
    public function updateCompanyAndAdmin($request, $uuid);
}
