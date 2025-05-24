<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterfaces\UserRepositoryInterface;
use App\Models\User;
use App\Traits\AuthExtension;
use App\Traits\FileHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    use AuthExtension, FileHandler;
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getAllUsers()
    {
        return $this->model->all();
    }

    public function getTeamMembers($company)
    {
        return $this->model->where('company_id', $company?->id)->get();
    }

    public function findTeamMember($uuid = '')
    {
        if (!$uuid) $uuid = '';
        return $this->model::findOrFailByUuid($uuid);
    }

    public function storeTeamMember($data, $company)
    {
        return $company->teams()->create($data);
    }

    public function createPassword($password, $memberUuid)
    {

        $member = $this->findTeamMember($memberUuid);
        $this->checkUserRole($member);

        $status = $member->forceFill([
            'password' => Hash::make($password),
        ])->save();

        if ($status) {
            // logout before redirecting to login if other users was logged in
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('message', __('passwords.created'));
        }
        return back()->with('message', __('passwords.not_created'));
    }


    public function updateProfile(User $member, $data)
    {


        if (request()->get('profile_picture')) {
            $baseUrl = config('filesystems.disks.s3.url');
            $data['profile_picture'] = str_replace($baseUrl . '/', '', $data['profile_picture']);
        }

        $member->fill($data);
        if ($member->save()) {

            if ($member->hasRole(User::ROLE_DESIGNER)) {
                $canProcure = !empty($data['can_procure']) && (bool)$data['can_procure'] === true;

                if ($canProcure && !$member->hasPermissionTo('can procure')) {
                    $member->givePermissionTo('can procure');
                } elseif (!$canProcure && $member->hasPermissionTo('can procure')) {
                    $member->revokePermissionTo('can procure');
                }
            }

            session()->flash('message', 'Profile update successfully');

            return response()->json([
                "error" => false,
                "message" => "Profile is updated successfully.",
            ], 200);
        }

        return response()->json([
            "error" => true,
            "message" => "Profile not updated"
        ], 500);
    }

    public function deleteMember($uuid)
    {
        try {
            $this->model::findOrFailByUuid($uuid)->delete();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
