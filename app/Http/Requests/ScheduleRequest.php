<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
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
        $isNewSupplier = !$this->filled('supplier_library_id');

        return [
            "brand_name" => "",
            "category_id" => "",
            "color" => "",
            "custom_specifications" => "",
            "custom_specifications.*.label" => "",
            "custom_specifications.*.custom_description" => "",
            "depth" => "",
            "description" => "",
            "doc_code" => "",
            "finish" => "",
            "height" => "",
            "price" => "",
            "item_name" => "",
            "length" => "",
            "product_url" => "",
            "project_id" => "",
            "quantity" => "",
            "sku" => "",
            "supplier.company_name" => $isNewSupplier ? 'required|string|max:255' : '',
            "supplier.name" => $isNewSupplier ? 'required|string|max:255' : '',
            "supplier.email" => $isNewSupplier ? 'required|email|max:255' : '',
            "supplier.phone_number" => $isNewSupplier ? 'required|string|max:15' : '',
            "supplier.address" => $isNewSupplier ? 'required|string|max:255' : '',
            "supplier.category" => $isNewSupplier ? 'required' : '',
            "supplier_library_id" => "",
            "width" => "",
            "attachments" => "",
            "attachments.*" => "",
            "cover_image" => "",
            "cover_image.*" => "",
            "material_uuid" => "",
            'project_areas' => 'nullable|array',
            'project_areas.*.project_area_id' => 'required_with:project_areas|integer|exists:project_areas,id',
            'project_areas.*.quantity' => 'required_with:project_areas|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'brand_name.required' => 'Brand name is required.',
            'description.required' => 'Description is required.',
        ];
    }

    /**
     * Custom attribute names.
     *
     * @return array
     */
    public function attributes()
    {
        return [
        ];
    }
}
