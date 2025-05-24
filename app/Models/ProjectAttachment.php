<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Attributes\AttachmentAttribute;

class ProjectAttachment extends Model
{
    use HasFactory, HasUUID, SoftDeletes, AttachmentAttribute;

    protected $appends = ["media_url"];
    protected $fillable = [
        'original_name',
        'name',
        'extension',
        'size',
        'project_id', 
        'is_main',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
