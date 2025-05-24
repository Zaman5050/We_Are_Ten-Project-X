<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\UserLeaveRepositoryInterface;
use App\Models\LeaveNegotiation;
use Illuminate\Support\Facades\DB;
use App\Models\UserLeave;
use Exception;

class UserLeaveRepository implements UserLeaveRepositoryInterface
{
    protected $model;

    public function __construct(UserLeave $userLeave)
    {
        $this->model = $userLeave;
    }

    public function applyForLeave(array $data)
    {
        DB::beginTransaction();
        try {
            $leave = $this->model::create($data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return $leave;
    }

    public function getLeavesByUserId(int $userId)
    {
        return $this->model::where('user_id', $userId)->get();
    } 

    public function updateLeave(int $id, array $data)
    {
        $leave = $this->model::findOrFail($id);
        $leave->update($data);
        return $leave;
    }

    public function deleteLeave(int $id)
    {
        $leave = $this->model::findOrFail($id);
        return $leave->delete();
    }

    public function updateStatus(array $data, string $leaveUuid)
    {
        DB::beginTransaction();
        try {
            $leave = $this->model::findByUuid($leaveUuid);
            if (auth()->user()->hasRole(auth()->user()::ROLE_ADMIN)) unset($data['user_id']);
            $leave->update($data);
            if (in_array($leave->status, ['negotiated', 're-negotiated'])) {
                LeaveNegotiation::create([
                    'leave_id' => $leave->id,
                    'user_id' => $leave->user_id,
                    'negotiation_type' => $leave->status,
                    'leave_type' => $data['leave_type'],
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'number_of_days' => $data['number_of_days'],
                    'notes' => $data['notes'],
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return $leave;
    }
    public function getAllUserLeavesToday()
    {
        return $this->model::whereDate('start_date', '<=', now()->toDateString())
                            ->whereDate('end_date', '>=', now()->toDateString())
                            ->where('status', 'approved')
                            ->whereHas('user', function($query) {
                                $query->where('company_id', auth()->user()->company_id); // Filter by company_id
                            })
                            ->with(['user', 'user.memberActiveProjects'])
                            ->get();
    }



}
