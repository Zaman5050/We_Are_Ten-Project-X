<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
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
            "text" => ["sometimes", "string", "max:500", "min:1"],
            "attachments" => [
                "sometimes", "array",
            ],
            "attachments.*" => [
                "sometimes", "image", "max:15360", //15360kb = 15mb
                "mimes:jpeg,jpg,png,gif,svg",
            ],
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
            'attachments.*' => 'attachment',
        ];
    }
}
