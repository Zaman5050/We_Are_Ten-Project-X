<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface TimesheetServiceInterface
{
    public function listing($request);
    public function timeLogged($request);
    public function updateTimerStatus($request);
    public function findTimesheet($uuid);
    public function updateTimeLogged($request);
    public function deleteTimeEntry($request);
    public function addEntryTime($request);
    public function getTimeSheetData($userId,$taskId);
    public function updateTimeLoggedForReassigned($request,$task);
}