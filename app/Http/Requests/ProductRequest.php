<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            "sku" => "",
            "supplier.company_name" => "",
            "supplier.name" => "",
            "supplier.email" =>  "",
            "supplier.phone_number" =>  "",
            "supplier.address" =>  "",
            "supplier_library_id" => "",
            "width" => "",
            "retail_price" => "",
            "trade_discount" => "",
            "trade_terms" => "",
            "production_lead_time" => "",
            "shipping_lead_time" => "",
            "notes" => "",
            "attachments" => "",
            "attachments.*" => "",
            "cover_image" => "",
            "markup" => "",
            "supplier_category_id" => "",
        ];
    }
}
