<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectGroupChatRequest extends FormRequest
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
        $rules = [
            'participant_uuid' => ['required', 'array'],
            'participant_uuid.*' => ['required', 'uuid' , 'exists:users,uuid'],
            'threatIndex' => ['required', 'uuid']
        ];
        return $rules;
    }

   
     /**
     * Custom attribute names.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'participant_uuid' => 'participants',
            'participant_uuid.*' => 'participants',
        ];
    }
}
