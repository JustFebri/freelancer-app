<?php

namespace App\Http\Requests\API;

use App\Models\sub_category;
use Illuminate\Foundation\Http\FormRequest;

class FilterSubCategoryRequest extends FormRequest
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
        $subCategoryModel = get_class(new sub_category());

        return [

            'subcategory_id' => "required|exists:{$subCategoryModel},subcategory_id",
            'type' => 'nullable|string|in:Digital Service,On-Site Service',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'lowRange' => 'nullable|numeric',
            'highRange' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }
}
