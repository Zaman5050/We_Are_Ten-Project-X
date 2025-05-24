<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Company;
use Illuminate\Validation\ValidationException;

 

trait AuthExtension
{
   
    public function checkUserRole(User|string|null $user, $role = 'designer'){
        $user = $this->getUserInstance($user);
        // Check if the user exists and has the specified role
        abort_unless($user && $user->hasRole($role) , 403, 'Forbidden: Invalid Permission.');
    }
    
    public function checkCompanyIsActive(User|string|null $user){
        $user = $this->getUserInstance($user);

        if($user && $user?->company?->status != Company::STATUS_ACTIVE){
            throw ValidationException::withMessages([
                "email" => 'You don\'t have access. Please contact support for assistance.'
            ]);
        }
    }

    private function getUserInstance(User|string|null $user){
        if(!($user instanceof User)){
           return User::where('email', $user)->with('company')?->first();
        }
        return $user;
    }
}
