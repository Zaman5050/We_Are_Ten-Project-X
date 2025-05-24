<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "index" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "flag" => $this->flag,
            "is_active" => (bool)$this->is_active,
            "title" => $this->title
        ];
    }
}
