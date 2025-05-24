<?php

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait LibraryAttachmentRelations
{
    
    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

}
