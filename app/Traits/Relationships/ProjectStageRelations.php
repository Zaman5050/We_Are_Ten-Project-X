<?php

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\ProjectStage;
use App\Models\Categories;
use App\Models\Procurement;
use App\Models\ProductLibrary;
use App\Models\Timesheet;
use App\Models\Task;



trait ProjectStageRelations
{


    public function stage()
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function procurements()
    {
        return $this->hasManyThrough(
            Procurement::class,
            ProductLibrary::class,
            'category_id',
            'product_library_id',
            'category_id',
        )->where('project_id', $this->project_id);
    }

    public function getRemainingAttribute()
    {
        $totalSpent = $this->procurements()
            ->selectRaw('SUM(procurements.actual_price * procurements.quantity) as total_spent')
            ->value('total_spent');
        return $this->amount - ($totalSpent ?? 0);
    }
    public function getRemainingQuantityAttribute()
    {
        $totalQtySpent = $this->procurements()->sum('quantity');
        return $this->quantity - $totalQtySpent;
    }

    public function timesheet(): HasManyThrough
    {
        return $this->hasManyThrough(
            Timesheet::class,
            Task::class,
            'stage_id',
            'project_task_id',
        );
    }
}
