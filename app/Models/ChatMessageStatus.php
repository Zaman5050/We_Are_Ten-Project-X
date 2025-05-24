<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessageStatus extends Model
{
    use HasFactory, HasUUID, SoftDeletes;

    protected $fillable = [
        'chat_message_id',
        'receiver_id',
        'seen'
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

    public function getReceiverDetail()
    {
        return $this->hasOne(User::class,'id','receiver_id');
    }
}
