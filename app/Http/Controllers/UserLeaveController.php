<?php

namespace App\Http\Controllers;

use App\Interfaces\ServiceInterfaces\Tenant\UserLeaveServiceInterface;
use App\Models\LeaveNotification as ModelsLeaveNotification;
use App\Models\UserLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\LeaveNotification;
use App\Services\Tenant\UserLeaveService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LeaveRequest;

class UserLeaveController extends Controller
{
    protected $service;

    public function __construct(UserLeaveServiceInterface $userLeaveService)
    {
        $this->service = $userLeaveService;
    }

    public function index()
    {
        $user = Auth::user();
        $notifications = ModelsLeaveNotification::where('notifiable_id', $user->id)
                                     ->where('notifiable_type', get_class($user))
                                     ->get();
        return view('includes.tenant.navbar', compact('notifications'));
    }

    public function applyLeave(LeaveRequest $request)
    {
        return $this->service->applyForLeave($request->validated());
    }

    public function showNotifications()
    {
        $notifications = auth()->user()->notifications;
        
        return response()->json($notifications);
    }

    public function getLeaveDetails($id)
    {
        $leave = UserLeave::findOrFail($id);
        return response()->json($leave);
    }

    public function updateStatus(Request $request)
    {
        return $this->service->updateStatus($request->all(),$request->leave_uuid);
    }
    // public function markAsRead(Request $request)
    // {
    //     $uuid = $request->route('uuid');
    //     $user = auth()->user()->unreadNotifications;
    //     dd($uuid,$user );
    //     // Find the specific notification by UUID in the unread notifications list
    //     $notification = $user->where('id', $uuid)->first();
    //     dd($notification);
    //     if ($notification) {
    //         Log::info('Notification found: ', ['notification' => $notification]);
    
    //         // Mark the notification as read
    //         $notification->markAsRead();
    
    //         return response()->json(['message' => 'Notification marked as read'], 200);
    //     }
    
    //     Log::error('Notification not found', ['uuid' => $uuid]);
    //     return response()->json(['message' => 'Notification not found'], 404);
    // }
    
    public function markAllAsRead(Request $request)
    {
        $unreadNotifications = auth()->user()->unreadNotifications;

        if (!$unreadNotifications->isEmpty()) {
            foreach ($unreadNotifications as $notification) {
                $notification->markAsRead();
            }
        }
        
        Log::info('All unread notifications marked as read', ['count' => $unreadNotifications->count()]);
        return response()->json(['message' => 'All unread notifications marked as read'], 200);
    }




}
