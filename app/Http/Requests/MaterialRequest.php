<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
            "category" => "",
            "category_id" => "",
            "color" => "",
            "custom_specifications" => "",
            "custom_specifications.*.label" => "",
            "custom_specifications.*.custom_description" => "",
            "depth" => "",
            "description" => "",
            "doc_code" => "",
            "finish" => "",
            "price" => "",
            "height" => "",
            "item_name" => "",
            "length" => "",
            "product_url" => "",
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
            "attachments" => "",
            "attachments.*" => "",
            "cover_image" => "",
            "cover_image.*" => "",
        ];
    }

    public function messages()
    {
        return [
            'brand_name.required' => 'Brand name is required.',
            'description.required' => 'Description is required.',
        ];
    }
}
