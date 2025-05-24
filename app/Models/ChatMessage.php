<?php

namespace App\Models;

use App\Traits\Relationships\ChatMessageRelations;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Attributes\ChatMessageAttributes;

class ChatMessage extends Model
{
    use HasFactory, HasUUID, SoftDeletes, ChatMessageRelations, ChatMessageAttributes;

    protected $fillable = ['chat_id', 'message', 'message_type', 'attachment', 'sender_id'];
    protected $casts = [];
    protected $appends = ['send_at'];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
