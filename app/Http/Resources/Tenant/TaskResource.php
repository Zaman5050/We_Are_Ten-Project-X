<?php

namespace App\Http\Resources\Tenant;

use App\Http\Resources\StatusResource;
use App\Http\Resources\Tenant\TeamResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tenant\Tasks\TaskAttachmentResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'task_uuid' => $this->uuid,
            'created_by' => $this->whenLoaded('owner') ? $this?->owner?->full_name : null,
            'stage' => $this->whenLoaded('stage') ? $this?->stage : null,
            'project_id' => $this->project_id,
            'stage_id' => $this->stage_id,
            'task_code' => $this->task_code,
            'title' => $this->title,
            'description' => $this->description,
            'flaged_by' => (bool) !empty($this->flaged_by),
            'archive' => (bool) $this->archive,
            'status_id' => $this->status_id,
            'status' => $this->whenLoaded('status') ? StatusResource::make($this?->status) : null,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'estimate_time' => $this->estimate_time ? $this->estimate_time_formated : $this->estimate_time,
            'order_number' => $this->order_number,
            'is_timer_paused' => (bool)$this->is_timer_paused,
            'attachments' => TaskAttachmentResource::collection($this->whenLoaded('attachments')),
            'members' => $this->whenLoaded('users') ? TeamResource::collection($this?->users) : collect(),
            'member_eligible' => $this->member_eligible,
            'timesheet' => $this->whenLoaded('timesheet', collect(),  collect()),
            'timer_mode' => $this->timer_mode,
            'total_time' => $this->total_time,
            'total_time_formated' => $this->total_time ? convertFromMinutes($this->total_time, true) : $this->total_time,
        ];
    }
}
