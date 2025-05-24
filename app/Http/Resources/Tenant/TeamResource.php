<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{

    public $preserveKeys = true;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "email" => $this->email,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "full_name" => $this->full_name,
            "short_name" => $this->short_name,
            "phone_number" => $this->phone_number,
            "timezone" => $this->timezone,
            "location" => $this->location,
            "status" => $this->status,
            "can_procure" => (bool)$this->can_procure,
            "role_name" => $this->role_name,
            "profile_picture" => $this->profile_picture,
            'company' => $this->company,
            "joining_date" => $this->joining_date,
            "leaving_date" => $this->leaving_date,
            "hourly_rate" => $this->hourly_rate,
            "sick_leaves" => $this->sick_leaves,
            "casual_leaves" => $this->casual_leaves,
            "annual_leaves" => $this->annual_leaves,
        ];
    }
}
