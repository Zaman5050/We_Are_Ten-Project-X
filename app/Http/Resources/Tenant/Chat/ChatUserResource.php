<?php

namespace App\Http\Resources\Tenant\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatUserResource extends JsonResource
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
            "full_name" => $this->full_name,
            "short_name" => $this->short_name,
            "profile_picture" => $this->profile_picture
        ];
    }
}
