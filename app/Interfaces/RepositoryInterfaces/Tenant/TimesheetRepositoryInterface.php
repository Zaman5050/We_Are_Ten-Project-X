<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;

use App\Models\User;


interface TimesheetRepositoryInterface
{
    public function findTimesheet($uuid);
    public function timeLogged($request, $task);
    public function getTimesheets(User $user, $page, $perPage, $startDate, $endDate);
    public function deleteTimeEntry($request);
    public function addTimeEntry($data, $task);    
    public function updateTimeLogged($request, $task);
    public function getTimeSheetData($userId, $taskId);
}
