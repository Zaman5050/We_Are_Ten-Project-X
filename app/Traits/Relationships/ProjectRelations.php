<?php

namespace App\Traits\Relationships;

use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use App\Models\ProjectStage;
use App\Models\ProjectTag;
use App\Models\Task;
use App\Models\Timesheet;
use App\Models\Chat;
use App\Models\ProjectAttachment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProjectArea;
use App\Models\ProcurementBudget;
use App\Models\ProjectMaterial;

trait ProjectRelations
{
   
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function owner()
    {
        return $this->belongsTo( User::class, 'owner_id');
    }

    public function currency()
    {
        return $this->belongsTo( Currency::class, 'currency_id');
    }

    public function tags()
    {
        return $this->hasMany( ProjectTag::class );
    }

    public function stages()
    {
        return $this->hasMany( ProjectStage::class );
    }

    public function members()
    {
        return $this->belongsToMany(User::class)
        ->whereHas('roles', function ($query) {
            $query->where('name', User::ROLE_DESIGNER);
        });
    }

    public function tasks()
    {
        return $this->hasMany( Task::class );
    }

    public function timesheet()
    {
        return $this->hasManyThrough(Timesheet::class, Task::class, 'project_id', 'project_task_id');
    }
    
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function membersOnLeave()
    {
        $currentDate = Carbon::now()->toDateString(); // Get the current date

        return $this->belongsToMany(User::class)
            ->whereHas('leaves', function ($leaveQuery) use ($currentDate) {
                $leaveQuery->where('status', 'approved') // Check if the leave is approved
                    ->whereDate('start_date', '<=', $currentDate) // Current date is after or on the start date
                    ->whereDate('end_date', '>=', $currentDate); // Current date is before or on the end date
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', User::ROLE_DESIGNER);
            });
    }

    public function attachment(): HasOne
    {
        return $this->hasOne(ProjectAttachment::class)->where('is_main', true);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    public function areas()
    {
        return $this->hasMany(ProjectArea::class);
    }
    public function procurementBudget()
    {
        return $this->hasMany(ProcurementBudget::class);
    }
    public function projectMaterial()
    {
        return $this->hasMany(ProjectMaterial::class);
    }
}
