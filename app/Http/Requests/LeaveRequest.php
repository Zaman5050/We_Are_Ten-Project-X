<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; 

class LeaveRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'number_of_days' => 'required|integer|min:1',
            'notes' => 'required|string',
            'status' => 'nullable|string|in:pending,approved,declined,negotiated,re-negotiated',
        ];
    }
}
