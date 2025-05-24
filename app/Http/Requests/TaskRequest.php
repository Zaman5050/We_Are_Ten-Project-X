<?php

namespace App\Http\Requests;

use App\Rules\EstimatTimeFormat;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StatusTypeExists;

class TaskRequest extends FormRequest
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
            'project_uuid' => ['nullable', 'string'],
            "title" => ['required', 'string', 'max:255'],
            "description" => ['required', 'string'],
            "stage_uuid" => ['required', 'uuid'], //'exists:project_stages,uuid'
            "member" => ['nullable', 'array'],
            "start_date" => ['required'],
            "due_date" => ['required'],
            "status_uuid" => ['required', 'uuid', new StatusTypeExists()],
            "attachments" => ["nullable", "array"],
            "attachments.*" => ["required", "uuid"],
            "estimate_time" => ["nullable", "string", new EstimatTimeFormat()],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'stage_uuid.required' => 'The stage is required.',
            'status_uuid.required' => 'The status is required.',
            'project_uuid.required' => 'The project is required.'
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
