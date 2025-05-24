<?php

namespace App\Traits\Attributes;
use Carbon\Carbon;


trait ProcurementAttributes
{
    
    public function setOrderDateAttribute($value)
    {
        $this->attributes['order_date'] =  formatDateForDatabase($value);
    }

    public function setShippingDateAttribute($value)
    {
        $this->attributes['shipping_date'] = formatDateForDatabase($value);
    }

    public function setDeliveryDateAttribute($value)
    {
        $this->attributes['delivery_date'] = formatDateForDatabase($value);
    }

    public function getOrderDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getShippingDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getDeliveryDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }


}
