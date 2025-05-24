<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\ProjectAttachmentResource;
use App\Http\Resources\Tenant\Chat\ChatResource;
use App\Http\Resources\Tenant\TeamResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string => $this->strin,  mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'uuid' => $this->uuid, 
            'name' => $this->name, 
            'code' => $this->code, 
            'address' => $this->address, 
            'measurement_unit' => $this->measurement_unit, 
            'type' => $this->type, 
            'is_procurement_enable' => $this->is_procurement_enable, 
            'start_date' => $this->start_date, 
            'due_date' => $this->due_date,
            'chats' => ChatResource::collection($this->whenLoaded('chats')),
            'members' => TeamResource::collection($this->whenLoaded('members')),
            'attachment' => ProjectAttachmentResource::make($this->whenLoaded('attachment')),
        ];
    }
}
