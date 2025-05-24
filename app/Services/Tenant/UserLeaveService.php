<?php

namespace App\Services\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\UserLeaveRepositoryInterface;
use App\Interfaces\ServiceInterfaces\Tenant\UserLeaveServiceInterface;
use App\Models\LeaveNotification as LeaveDatabaseNotification;
use App\Notifications\LeaveNotification;
use Illuminate\Support\Facades\Log;
use Exception;

class UserLeaveService implements UserLeaveServiceInterface
{
    protected $repository;

    public function __construct(UserLeaveRepositoryInterface $userLeaveRepositoryInterface)
    {
        $this->repository = $userLeaveRepositoryInterface;
    }

    public function applyForLeave(array $data)
    {
        try {
            $leave = $this->repository->applyForLeave($data);
            auth()?->user()?->company?->admin?->notify(new LeaveNotification($leave, 'applied'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Failed to apply for leave: ' . $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json([
            'message' => 'Leave is applied successfully.',
            'alert-type' => 'success',
            'leave' => $leave,
        ], 200);
    }

    public function getUserLeaves($userId)
    {
        return $this->repository->getLeavesByUserId($userId);
    }

    public function updateStatus(array $data, string $leaveUuid)
    {
        try {
            $leave = $this->repository->updateStatus($data, $leaveUuid);
            $notifications = LeaveDatabaseNotification::where('data->uuid', $leaveUuid)->get();

            foreach ($notifications as $notification) {
                $notificationData = $notification->data;
                $notificationData['status'] = $data['status'] ?? $leave->status;
                $notification->update(['data' => $notificationData]);
            }
            $notifiable = auth()->user()->hasRole(auth()->user()::ROLE_DESIGNER) ? auth()?->user()?->company?->admin : $leave->user;
            $notifiable?->notify(new LeaveNotification($leave, $leave->status));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Failed to updating leave status: ' . $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json([
            'message' => 'Leave status is updated successfully.',
            'alert-type' => 'success',
            'leave' => $leave,
        ], 200);
    }
    public function getAllUserLeavesToday()
    {
        return $this->repository->getAllUserLeavesToday();
    }
}
