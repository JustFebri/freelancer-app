<?php

namespace App\Http\Requests\API;

use App\Models\service;
use Illuminate\Foundation\Http\FormRequest;

class CustomOrderRequest extends FormRequest
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

        return [
            'service_id' => "required|exists:{$serviceModel},service_id",
            'description' => "required|string|max:500",
            'price' => "required|numeric",
            'revision' => 'required|numeric',
            'delivery_days' => 'required|numeric',
            'expiration_date' => 'nullable|date',
            'onsite_date' => 'nullable|date',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'loc' => 'nullable|string|max:255',
        ];
    }
}
