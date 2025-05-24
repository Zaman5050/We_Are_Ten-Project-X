<?php

namespace App\Models;

use App\Traits\Relationships\ChatRelations;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Attributes\ChatAttributes;

class Chat extends Model
{
    use HasFactory, HasUUID, SoftDeletes, ChatRelations, ChatAttributes;

    protected $fillable = ['project_id', 'title', 'last_message', 'last_messaged_at'];
    protected $casts = [
        'last_messaged_at' => 'datetime',
    ];
    protected $appends = ['unseen_messages_count'];    

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
