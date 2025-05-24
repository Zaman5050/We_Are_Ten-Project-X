<?php

namespace App\Http\Requests\Teams;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string|regex:/^\+(\d\s?){10,13}$/',
            'can_procure' => 'required|boolean',
            'location' => 'required|string',
            'timezone' => 'required|string',
            'profile_picture' => 'nullable',
            'joining_date' => 'nullable',
            'leaving_date' => 'nullable',
            'hourly_rate' => 'nullable',
            'sick_leaves' => 'nullable',
            'casual_leaves' => 'nullable',
            'annual_leaves' => 'nullable',
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
            'can_procure' => 'designtation',
            'attachments' => 'profile image',
            'attachments.*' => 'profile image',
        ];
    }
}
