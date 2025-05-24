<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\UserLeave;
use App\Events\LeaveNotificationEvent;

class LeaveNotification extends Notification
{
    use Queueable;

    protected $leave;
    protected $status;

    public function __construct(UserLeave $leave, $status)
    {
        $this->leave = $leave;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $response = [
            'uuid' => $this->leave->uuid,
            'leave_id' => $this->leave->id,
            'user_uuid' => $this->leave?->user?->uuid,
            'full_name' => $this->leave?->user?->full_name,
            'status' => $this->leave->status,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'leave_type' => $this->leave->leave_type, // Ensure leave_type is included
            'notes' => $this->leave->notes,
            'message' => 'Leave request ' . $this->status . '.',
        ];
        broadcast(new LeaveNotificationEvent($response))->toOthers();
        return $response;
    }
}
