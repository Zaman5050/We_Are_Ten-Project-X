<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Searchable;
use App\Traits\Relationships\TaskRelations;
use App\Traits\Attributes\TaskAttributes;

class Task extends Model
{
    use HasFactory,
    HasUUID,
    SoftDeletes,
    Searchable,
    TaskRelations,
    TaskAttributes;

    public $searchable_columns = ['task_code', 'title', 'description'];


    protected $fillable = [
        'created_by',
        'stage_id',
        'task_code',
        'title',
        'description',
        'flaged_by',
        'archive',
        'status_id',
        'project_id',
        'start_date',
        'due_date',
        'estimate_time',
        'order_number',
        'is_timer_paused', 
        'timer_mode'
    ];
    protected $casts = [];
    protected $table = 'project_tasks';
    protected $appends = ['completion_percentage', 'total_time', 'estimate_time_formated', 'member_eligible'];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }    
    
    public function scopeMemberFilter($query, $data)
    {
        $assignee = explode(',', @$data['assignee']) ?: [];
        $assignee = array_filter($assignee, fn ($x) => !!$x);
        
        $query->when(!empty($assignee), function ($q) use ($assignee) {
            $assigneeIds = User::whereIn('uuid', $assignee)->get()->pluck('id')->toArray();
            $q->whereHas('users', fn($q) => $q->whereIn('user_id', $assigneeIds));
        });
        return $query;
    }

    public function scopeSearchFilter($query, $data)
    {
        $term = @$data["search"] ?: null;
        if (!empty($term)) {
            $term = replaceDashWithNDash($term);
            $terms = explode(" ", trim($term));
            foreach($terms as $term){
                $query->whereAnyColumnLike($term);
            }
        }
        return $query;
    }

}
