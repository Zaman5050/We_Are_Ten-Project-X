<?php

namespace App\Traits\Relationships;

use App\Models\MaterialLibrary;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectArea;

trait ProjectMaterialRelations
{
   
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function material()
    {
        return $this->belongsTo(MaterialLibrary::class,'material_library_id')->with('category','supplier','specifications','attachments','cover_image');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function areas()
    {
        return $this->belongsToMany(ProjectArea::class,'project_material_areas')->withPivot('quantity');
    }


    
}
