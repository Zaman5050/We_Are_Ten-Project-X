<?php

namespace App\Traits\Relationships;

use App\Models\User;
use App\Models\ChatMessageStatus;

trait ChatMessageRelations
{
    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    public function getChatMessagesStatus()
    {
        return $this->hasMany(ChatMessageStatus::class,'chat_message_id','id')->where('receiver_id', auth()->id());
    }
}
