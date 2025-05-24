<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            "uuid" => $this->uuid,
            "original_name" => $this->original_name,
            "media_url" => $this->media_url,
            "extension" => $this->extension,
            "size" => $this->size,
            "is_main" => $this->is_main,
        ];
    }
}
