<?php

namespace App\Http\Resources\Tenant\Chat;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ChatMessageResuouce;
use App\Http\Resources\Project\ProjectResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tenant\Chat\ChatUserResource;
 
class ChatResource extends JsonResource
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
            "title" => $this->title,
            "last_message" => $this->last_message ?? Str::limit(request()?->text, 60),
            "last_messaged_at" => $this->last_messaged_at ?? now(),
            "unseen_messages_count" => $this->unseen_messages_count,
            "project_chat_members_count" => $this?->project_chat_members_count,
            "project" => ProjectResource::make($this->whenLoaded("project")),
            "project_chat_members" => ChatUserResource::collection($this->whenLoaded("projectChatMembers")),
            "chat_messages" => ChatMessageResuouce::collection($this->whenLoaded('chatMessages')),
        ];
    }
}
