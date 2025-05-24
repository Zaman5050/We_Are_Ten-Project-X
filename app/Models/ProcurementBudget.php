<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Relationships\ProjectStageRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementBudget extends Model
{
    use HasFactory, HasUUID, ProjectStageRelations, SoftDeletes;

    protected $fillable = [
        'project_id', 'category_id', 'quantity', 'amount'
    ];
    protected $appends = ['remaining','remainingQuantity'];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

}
