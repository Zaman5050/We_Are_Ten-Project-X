<?php

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\CustomSpecification;
use App\Models\LibraryAttachment;
use App\Models\Categories;
use App\Models\SupplierLibrary;
use App\Models\Company;
use App\Models\ProjectMaterial;

trait MaterialLibraryRelations
{
    public function specifications(): MorphMany
    {
        return $this->morphMany(CustomSpecification::class, 'specificationable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(LibraryAttachment::class, 'attachmentable')->where('label','product_file');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierLibrary::class, 'supplier_library_id');
    }
    
    public function cover_image(): MorphMany
    {
        return $this->morphMany(LibraryAttachment::class, 'attachmentable')->where('label','product_image');
    }

    public function schedules()
    {
        return $this->hasMany(ProjectMaterial::class);
    }

}
