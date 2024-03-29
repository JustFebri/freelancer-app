<?php

namespace App\Http\Requests\API;

use App\Models\service;
use App\Models\sub_category;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
        $serviceModel = get_class(new service());
        $subCategoryModel = get_class(new sub_category());

        return [
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'serviceId' => "required|exists:{$serviceModel},service_id",
            'title' => 'required|string|max:255',
            'desc' => 'required|string|max:1200',
            'type' => 'required|string|max:255',
            'customOrder' => 'required|in:true,false',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'location' => 'nullable|string|max:255',
            'packages' => 'required|string',
            'subCategory' => "required|string|exists:{$subCategoryModel},subcategory_name",
        ];
    }
}
