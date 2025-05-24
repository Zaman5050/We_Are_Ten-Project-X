<?php

namespace App\Traits\Relationships;

use App\Models\ProductLibrary;
use App\Models\SupplierLibrary;
use App\Models\MaterialLibrary;
use App\Models\ProcurementBudget;

trait CategoryRelations
{

    public function products()
    {
        return $this->hasMany(ProductLibrary::class, 'category_id');
    }

    public function materials()
    {
        return $this->hasMany(MaterialLibrary::class, 'category_id');
    }
    public function suppliers()
    {
        return $this->belongsToMany(
            SupplierLibrary::class,
            'category_supplier_library',
            'category_id',
            'supplier_library_id'
        );
    }
    public function procurmentBudgetCategory()
    {
        return $this->hasMany(ProcurementBudget::class, 'category_id');
    }
}
