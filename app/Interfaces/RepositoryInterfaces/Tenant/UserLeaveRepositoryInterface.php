<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;


interface UserLeaveRepositoryInterface
{
    public function applyForLeave(array $data);
    public function getLeavesByUserId(int $userId);
    public function updateStatus(array $data,string $leaveUuid);
    public function getAllUserLeavesToday();

    
}
