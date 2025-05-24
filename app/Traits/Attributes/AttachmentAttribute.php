<?php

namespace App\Traits\Attributes;

use Illuminate\Support\Facades\Storage;

trait AttachmentAttribute
{
    protected function getMediaUrlAttribute()
    {
        return Storage::url($this->name);
    }
}
