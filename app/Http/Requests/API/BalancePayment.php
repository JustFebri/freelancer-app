<?php

namespace App\Http\Requests\API;

use App\Models\freelancer;
use Illuminate\Foundation\Http\FormRequest;

class BalancePayment extends FormRequest
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
        $freelancerModel = get_class(new freelancer());

        return [
            'type' => 'required|string|max:255',
            'itemId' => 'required',
            'freelancer_id' => "required|exists:{$freelancerModel},freelancer_id",
            'price' => 'required|numeric',
            'onsite_date' => 'nullable|date',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'loc' => 'nullable|string|max:255',
        ];
    }
}
