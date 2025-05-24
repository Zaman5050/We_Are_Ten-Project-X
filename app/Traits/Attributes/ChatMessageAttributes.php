<?php

namespace App\Traits\Attributes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

trait ChatMessageAttributes
{

    public function getSendAtAttribute($value)
    {
        return Carbon::parse($this->created_at)->timezone('Asia/Karachi')->format('g:i A');
    }
    
    public function getAttachmentAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
    
}
