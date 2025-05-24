<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            "name" => ['required', 'string', 'max:255'],
            "email" => ['required', 'email', 'max:255'],
            "address" => ['nullable', 'string', 'max:255'],
            "phone_number" => ['required', 'string', 'max:15'],
            "currency_id" => ['required'], 
            "trade_discount" => ['nullable', 'numeric', 'min:0', 'max:100'],
            "timezone" => ['required', 'string'],  
            "website" => ['nullable', 'url'],
            "company_name" => ['required'],
            "company_id" => ['nullable'],
            "itemSold" => ['required'],  
            'profile_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            "name.required" => "Contact name is required.",
            "email.required" => "Email is required.",
            "email.email" => "The email format is invalid.",
            "phone_number.required" => "Phone number is required.",
            "currency.required" => "Currency is required.",
            "currency_id.required" => "Currency is required.",
            "timezone.required" => "Please select a valid timezone.",
            "company_id.exists" => "The selected company does not exist.",
            "profile_file.image" => "The profile picture must be an image file.",
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
            'project_uuid' => 'project',
            'stage_uuid' => 'stage',
            'status_uuid' => 'status',
        ];
    }
}
