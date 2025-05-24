<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tenant\Chat\ChatUserResource;

class ChatMessageResuouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "index" => $this->id,
            "message" => $this->message,
            "message_type" => $this->message_type ?? "text",
            "attachment" => $this->attachment,
            "send_at" => $this->send_at,
            "sender" => new ChatUserResource($this->whenLoaded("sender")),
            "messages_seen_status_count" => $this?->get_chat_messages_status_count,
        ];
    }
}