<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ExchangeRate;

class ExchangeRateRequest extends FormRequest
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
        // Adjust 'exchange_rate' to match your route parameter name
        return [
            'base_currency_id' => 'required',
            'quote_currency_id' => 'required',
            'exchange_rate' => 'required|numeric|min:0',
        ];
    }


    public function messages()
    {
        return [];
    }

    /**
     * Custom attribute names.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }
}
