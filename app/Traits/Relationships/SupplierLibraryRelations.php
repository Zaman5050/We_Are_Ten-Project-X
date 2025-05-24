<?php

namespace App\Traits\Relationships;

use App\Models\Categories;

trait SupplierLibraryRelations
{
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'category_supplier_library', 'supplier_library_id', 'category_id');
    }

}
