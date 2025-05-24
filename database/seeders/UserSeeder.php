<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try
        {
            $company = Company::create([
                'uuid' => HasUUID::generateUUID(),
                'name' => 'Super Admin Company',
                'email' => 'company@projectx.com',
                'subdomain' => 'company',
            ]);
            if(!User::where('email', config('app.super_admin_email'))->exists()){
                $superAdmin = User::create([
                    'uuid' => HasUUID::generateUUID(),
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'email' => config('app.super_admin_email'),
                    'password' => Hash::make('password'),
                    'company_id' => $company->id,
                ]);
                $superAdmin->assignRole('super-admin');
            }

            if(!User::where('email', 'admin@projectx.com')->exists()){
                $admin = User::create([
                    'uuid' => HasUUID::generateUUID(),
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'email' => 'admin@projectx.com',
                    'password' => Hash::make('password'),
                    'company_id' => $company->id,
                ]);
                $admin->assignRole('admin');
            }

            if(!User::where('email', 'designer@projectx.com')->exists()){
                $desinger = User::create([
                    'uuid' => HasUUID::generateUUID(),
                    'first_name' => 'Designer',
                    'last_name' => 'User',
                    'email' => 'designer@projectx.com',
                    'password' => Hash::make('password'),
                    'company_id' => $company->id,
                    'can_procure' => 1,
                ]);
                $desinger->assignRole('designer');
                $procurementPermission = Permission::first();
                $desinger->givePermissionTo($procurementPermission);
            }

        }
        catch(Exception $e)
        {
            DB::rollBack();
            echo $e->getMessage();
        }
        DB::commit();
    }
}
