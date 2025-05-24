<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;


interface UserLeaveServiceInterface
{
    public function applyForLeave(array $data);
    public function getUserLeaves($userId);
    public function updateStatus(array $data,string $leaveUuid);
    public function getAllUserLeavesToday();

}
