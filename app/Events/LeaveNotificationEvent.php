<?php

namespace App\Events;

use App\Models\UserLeave;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\User;

class LeaveNotificationEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $leaveResponse;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($leaveResponse)
    {
        logger(__CLASS__);
        $this->leaveResponse = $leaveResponse;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        if (auth()->user()->hasRole(auth()->user()::ROLE_ADMIN)) {
            return new PrivateChannel('tenant-leave-channel.' . $this->leaveResponse['user_uuid'] ?? null);
        }
        $admin = User::role(User::ROLE_ADMIN)->where('company_id', auth()->user()->company_id)->first();
        return new PrivateChannel('tenant-leave-channel.' . $admin?->uuid ?? null);
    }

    public function broadcastAs()
    {
        return 'leaveNotification';
    }
}
