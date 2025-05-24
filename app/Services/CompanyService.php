<?php

namespace App\Services;

use App\Interfaces\ServiceInterfaces\CompanyServiceInterface;
use App\Interfaces\RepositoryInterfaces\CompanyRepositoryInterface;
use Illuminate\Validation\Rules;
use App\Models\User;

class CompanyService implements CompanyServiceInterface
{
    protected $repository;

    public function __construct(CompanyRepositoryInterface $companyRepositoryInterface)
    {
        $this->repository = $companyRepositoryInterface;
    }

    public function registerCompany($request)
    {
        $validateData = $request->validate([
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $exists = \App\Models\Company::where('name', $value)->exists();
                        if ($exists) {
                            $fail("Company name '{$value}' is already in use.");
                        }
                    }
                }
            ],
            'email' => ['required', 'email', 'unique:companies,email'],
            'subdomain' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        $urlWithoutProtocol = preg_replace('~^https?://~', '', $value);

                        $subdomain = strtok($urlWithoutProtocol, '.');
                        $exists = \App\Models\Company::where('subdomain', $subdomain)->exists();

                        if ($exists) {
                            $fail("The virtual domain '{$value}' is already in use.");
                        }
                    }
                }
            ],
        ]);
        return $this->repository->register($validateData);
    }

    public function storeCompanyAdmin($data)
    {
        $validateData = $data->validate([
            'company_uuid' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        return $this->repository->storeCompanyAdmin($validateData);
    }

    public function getAllCompanies()
    {
        return $this->repository->getAllCompanies();
    }

    public function findCompany($uuid)
    {
        return $this->repository->findCompany($uuid);
    }

    public function toggleCompanyStatus($uuid)
    {
        return $this->repository->toggleCompanyStatus($uuid);
    }

    public function changePassword($request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'old_password' => 'required|string',
        ]);

        return $this->repository->changePassword($request, $user);
    }

    public function updateCompanyAndAdmin($request, $uuid)
    {
        return $this->repository->updateCompanyAndAdmin($request, $uuid);
    }
}
