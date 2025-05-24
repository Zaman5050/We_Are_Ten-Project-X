<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcurementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "brand_name" => "",
            "category_id" => "required",
            "color" => "",
            "custom_specifications" => "",
            "custom_specifications.*.label" => "",
            "custom_specifications.*.custom_description" => "",
            "depth" => "",
            "description" => "",
            "finish" => "",
            "height" => "",
            "product_name" => "",
            "length" => "",
            "product_url" => "",
            "material" => "",
            "project_id" => "",
            "quantity" => "",
            "sku" => "",
            "supplier.company_name" => "",
            "supplier.name" => "",
            "supplier.email" =>  "",
            "supplier.phone_number" =>  "",
            "supplier.address" =>  "",
            "supplier_library_id" => "",
            "width" => "",
            "order_date" => "",
            "quote_currency_id" => "",
            "base_currency_id" => "",
            "exchange_rate" => "",
            "retail_price" => "",
            "trade_discount" => "",
            "trade_terms" => "",
            "production_lead_time" => "",
            "shipping_lead_time" => "",
            "order_date" => "",
            "shipping_date" => "",
            "delivery_date" => "",
            "notes" => "",
            "product_uuid" => "",
            "project_area_id" => "",
            "attachments" => "",
            "attachments.*" => "",
            "cover_image" => "",
            "markup" => "",
            "supplier_category_id" => "",
            "area.area_code" => "",
            "area.area_dimention" => "",
            "area.area_name" =>  "",

        ];
    }
}
