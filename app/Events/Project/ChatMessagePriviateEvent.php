<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ChatMessagePriviateEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    public $receiverIds = [];
    public $newChat;
    public $senderId;
    public $messageDateTime;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $newChat, $senderId, $receiverIds)
    {
        logger(__CLASS__);
        $this->message = $message;
        $this->newChat = $newChat;
        $this->senderId = $senderId;
        $this->receiverIds = $receiverIds;
        $this->messageDateTime = Carbon::parse(now())->timezone('Asia/Karachi')->format('g:i A');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $receiverChannelsWithIds = collect($this->receiverIds)->map(function ($receiverId) {
            return new PrivateChannel('project-chat-channel.' . $receiverId);
        })->unique()->toArray();

        logger($receiverChannelsWithIds);

        return $receiverChannelsWithIds;
    }

    public function broadcastAs(): string
    {
        return 'project-private-chat-message';
    }
}
