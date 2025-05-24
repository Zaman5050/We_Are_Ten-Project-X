<?php

namespace App\Traits\Relationships;

use App\Models\ChatMessage;
use App\Models\Project;
use App\Models\User;

trait ChatRelations
{

    public function projectChatMembers()
    {
        return $this->belongsToMany(User::class, 'chat_user');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id','project_id');
    }
}
