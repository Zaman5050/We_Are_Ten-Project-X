<?php

namespace App\Traits\Relationships;

use App\Models\ProductLibrary;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Project;
use App\Models\ProjectArea;

trait ProcurementRelations
{

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductLibrary::class, 'product_library_id')->with('category', 'supplier', 'specifications','attachments','cover_image');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectArea()
    {
        return $this->belongsTo(ProjectArea::class);
    }

    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function exchangeCurrency()
    {
        return $this->belongsTo(Currency::class,'quote_currency_id');
    }
}
