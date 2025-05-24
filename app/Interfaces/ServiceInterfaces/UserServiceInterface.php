<?php

namespace App\Interfaces\ServiceInterfaces;



interface UserServiceInterface
{
    public function getAllUsers();
    public function getTeamMembers($company);
    public function findTeamMember($uuid);
    public function createTeamMember($request, $company);
    public function createPassword($request);
    public function createPasswordView($request);
    public function getProfileMember($uuid);
    public function updateProfile($uuid, $data);
    public function getMemberPojects($uuid);
    public function getMemberTasks($uuid);
    public function deleteMember($uuid);
}
