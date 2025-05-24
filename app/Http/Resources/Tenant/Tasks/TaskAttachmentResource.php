<?php

namespace App\Http\Resources\Tenant\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskAttachmentResource extends JsonResource
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
            "original_name" => $this->original_name,
            "media_url" => $this->media_url,
            "extension" => $this->extension,
            "size" => $this->size,
        ];
    }
}
