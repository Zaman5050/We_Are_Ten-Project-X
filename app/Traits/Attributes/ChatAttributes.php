<?php

namespace App\Traits\Attributes;

trait ChatAttributes
{

    public function getUnseenMessagesCountAttribute()
    {
        if($this?->chatMessages->count() > 0){
            return $this?->chatMessages?->where('get_chat_messages_status_count', true)->count();
        }
        return 0;
    }
}
