<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
        ];
    }

    protected function prepareForValidation()
    {
        $validator = $this->getValidatorInstance();

        $validator->after(function ($validator) {
            if ($this->email === config('app.super_admin_email')) {
                $validator->errors()->add('email', 'Password reset is not allowed for this email address.');
            }
        });
    }
}
