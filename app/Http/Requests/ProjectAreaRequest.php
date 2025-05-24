<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueAreaCodeForProjectRule;

class ProjectAreaRequest extends FormRequest
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
        $projectId = request()->project;
        $areas = $this->input('areas', []);

        return [
            'areas' => 'required|array',
            'areas.*.uuid' => 'required|string',
            'areas.*.area_name' => 'required|string',
            'areas.*.area_dimention' => 'required|string',
            'areas.*.area_code' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($projectId, $areas) {
                    // Get the current UUID for the field being validated
                    $areaIndex = str_replace('areas.', '', $attribute);
                    $areaIndex = str_replace('.area_code', '', $areaIndex);
                    $currentUuid = $areas[$areaIndex]['uuid'] ?? null;
                    
                    // Apply the custom rule
                    $rule = new UniqueAreaCodeForProjectRule($projectId, $currentUuid);
                    $rule->validate($attribute, $value, $fail);
                },
            ],
        ];
    }
}
