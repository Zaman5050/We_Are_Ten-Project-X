<?php

namespace App\Http\Requests\Timesheet;

use Illuminate\Foundation\Http\FormRequest;

class AddEntryTimeRequest extends FormRequest
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
            "task_uuid" => ['required', 'string', 'exists:project_tasks,uuid'],
            "project_uuid" => ['required', 'string', 'exists:projects,uuid'],
            "start_date" => ['required', 'string'],
            "end_date" => ['required', 'string'],
            "start_time" => ['required', 'string'],
            "end_time" => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'task_uuid.required' => 'The task filed of project is required.',
            'project_uuid.required' => 'The project filed is required.',
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
            'task_uuid' => 'task',
        ];
    }

}
