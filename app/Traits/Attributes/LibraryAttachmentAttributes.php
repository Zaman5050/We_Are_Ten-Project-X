<?php

namespace App\Traits\Attributes;
use Illuminate\Support\Facades\Storage;


trait LibraryAttachmentAttributes
{
    
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

}
