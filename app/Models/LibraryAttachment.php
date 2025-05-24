<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\LibraryAttachmentRelations;
use App\Traits\Attributes\LibraryAttachmentAttributes;

class LibraryAttachment extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        LibraryAttachmentRelations,
        LibraryAttachmentAttributes;

    protected $fillable = [
        'attachmentable_id',
        'attachmentable_type',
        'label',
        'original_name',
        'path',
    ];
    protected $casts = [];
    protected $appends = ['url'];    

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
