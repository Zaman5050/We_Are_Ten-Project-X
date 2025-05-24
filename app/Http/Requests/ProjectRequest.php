<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'address' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'currency_id' => 'nullable|integer',
            'measurement_unit' => 'required',
            'member_ids' => 'nullable',
            'tags' => 'nullable',
            'description' => 'nullable',
            'stages' => 'nullable',
            'start_date' => 'nullable',
            'due_date' => 'nullable',
            'attachments' => 'nullable|array',
            'attachments.*' => 'required|uuid',
            'is_procurement_enable' => 'nullable',
        ];
        if ($this->input('status') === 'draft') {
            // Remove the required validation for certain fields
            $rules['name'] = 'nullable|string|max:255';  // Optional for draft
            $rules['type'] = 'nullable|string';  
            $rules['currency_id'] = 'required|integer';  
            $rules['address'] = 'nullable|string';  
            $rules['measurement_unit'] = 'nullable';  
            $rules['description'] = 'nullable';   
            $rules['start_date'] = 'nullable';      
            $rules['due_date'] = 'nullable';        
            $rules['member_ids'] = 'nullable|array';     
            $rules['tags'] = 'nullable|array';           
            $rules['stages'] = 'nullable|array';         
            $rules['status'] = 'nullable';         
            $rules['is_procurement_enable'] = 'sometimes';         
            $rules['attachments'] = 'nullable|array';    
            $rules['attachments.*'] = 'nullable|uuid';         
        }
        return $rules;
    }


    public function messages()
    {
        return [
            'name.required' => 'The project is required.',
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
            'name' => 'project name',
            'description' => 'description',
        ];
    }

}
