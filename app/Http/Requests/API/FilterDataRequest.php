<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class FilterDataRequest extends FormRequest
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
            'keyword' => 'required|string|max:255',
            'type' => 'nullable|string|in:Digital Service,On-Site Service',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'lowRange' => 'nullable|numeric', 
            'highRange' => 'nullable|numeric', 
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }
}
