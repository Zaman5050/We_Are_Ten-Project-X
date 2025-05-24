<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterfaces\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Events\CompanyRegisteredEvent;
use App\Traits\AuthExtension;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Events\AdminChangePasswordEvent;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompanyRepository implements CompanyRepositoryInterface
{
    use AuthExtension;

    protected $model;

    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    public function register($requestData)
    {
        DB::beginTransaction();
        try {
            $company = $this->model::updateOrCreate([
                "email" => $requestData["email"],
            ], $requestData);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return to_route('super-admin.company.createCompanyAdmin', ['company_uuid' => $company->uuid])->with([
            'message' => 'Company is registered successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function findCompany($uuid = '')
    {
        if (!$uuid) $uuid = '';
        return $this->model::findOrFailByUuid($uuid);
    }

    public function getAllCompanies()
    {
        return $this->model::with('admin')->latest()->get();
    }

    public function storeCompanyAdmin($data)
    {
        DB::beginTransaction();
        try {
            $company = $this->findCompany($data['company_uuid']);
            $company->load(['admin']);

            list($firstName, $lastName) = explode(' ', $data['name'] . ' ', 2);

            $admin = $company->admin()
                ->where('email', $company->email)
                ->create([
                    'email' => $data['email'],
                    'company_id' => $company->id,
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'joining_date' => Carbon::now()->format('d/m/Y')
                ]);
            $admin->assignRole('admin');
            $admin->decryptedPassword = $data['password'];
            event(new CompanyRegisteredEvent($company, $admin));
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        DB::commit();

        return to_route('super-admin.dashboard')->with([
            'message' => 'Admin is created successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function toggleCompanyStatus($uuid)
    {
        DB::beginTransaction();
        try {
            $company = $this->findCompany($uuid);
            $company->update([
                'status' => $company->status == Company::STATUS_ACTIVE ? Company::STATUS_IN_ACTIVE : Company::STATUS_ACTIVE
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        DB::commit();

        return to_route('super-admin.dashboard')->with([
            'message' => 'Company is ' . ($company->status === Company::STATUS_ACTIVE ? 'activated' : 'deactivated') . ' successfully.',
            'alert-type' => 'success',
        ]);
    }


    public function changePassword($request, User $user)
    {
        DB::beginTransaction();

        $response = [
            "status" => 500,
            "error" => true,
            "message" => __('passwords.user')
        ];

        try {
            $user->load(['company']);
            // $this->checkUserRole($user, 'super-admin');

            // Check if the old password matches the one in the database
            // if (!Hash::check($request->old_password, $user->password)) {
            //     throw ValidationException::withMessages([
            //         'old_password' => ["Old password does not match."]
            //     ]);
            // }

            // Check if the new password is the same as the old password
            if (Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['New password cannot be the same as the old password.']
                ]);
            }

            $user->password = $request->password;
            $user->password_changed = true;
            if ($user->save()) {
                $response['status'] = 200;
                $response['error'] = false;
                $response['message'] = "{$user?->company?->name} password has been updated successfully";

                $user->decryptedPassword = $request->password;
                event(new AdminChangePasswordEvent($user?->company, $user));

                session()->flash('message', "{$user?->company?->name} password has been updated successfully");
                $response['redirect'] = route('super-admin.dashboard');
            }

            DB::commit();

            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            DB::rollBack();
            $response['message'] = $e->getMessage();
            return response()->json($response, $response['status']);
        }
    }
    public function updateCompanyAndAdmin($request, $uuid)
    {
        DB::beginTransaction();
        try {
            $company = $this->findCompany($uuid);
            $company->load('admin'); // Load admin relationship

            $company->update([
                'name' => $request->name,
                'email' => $request->email,
                'virtual_url' => $request->subdomain,
            ]);
            // dd($company->admin, $req);
            if ($company->admin) {
                // Split admin_name into first and last name
                $names = explode(' ', $request->admin_name, 2); // Assuming space separates first and last names
                $firstName = $names[0];
                $lastName = isset($names[1]) ? $names[1] : ''; // If there's no last name, use an empty string
                $company->admin->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $request->admin_email,
                ]);
            }

            DB::commit();

            return redirect()->route('super-admin.dashboard')->with([
                'message' => 'Company and Admin information updated successfully!',
                'alert-type' => 'success',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'message' => 'Error updating company: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
