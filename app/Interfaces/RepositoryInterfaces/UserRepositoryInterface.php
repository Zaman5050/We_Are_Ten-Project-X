<?php

namespace App\Interfaces\RepositoryInterfaces;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function getTeamMembers($company);
    public function findTeamMember($uuid);
    public function storeTeamMember($data, $company);
    public function createPassword($password, $memberUuid);
    public function updateProfile(User $member, $data);
    public function deleteMember($uuid);
}
