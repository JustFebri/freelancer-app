<?php

namespace App\Http\Requests\API;

use App\Models\custom_orders;
use App\Models\freelancer;
use App\Models\service;
use App\Models\user;
use Illuminate\Foundation\Http\FormRequest;

class MidtransCustomPayment extends FormRequest
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
        $customModel = get_class(new custom_orders());
        $serviceModel = get_class(new service());
        $freelancerModel = get_class(new freelancer());
        $userModel = get_class(new user());

        return [
            'custom_id' => "required|exists:{$customModel},custom_id",
            'service_id' => "required|exists:{$serviceModel},service_id",
            'freelancer_id' => "required|exists:{$freelancerModel},freelancer_id",
            'email' => "required|string|exists:{$userModel},email",
            'price' => 'required|numeric',
            'name' => 'required|string|max:255',
        ];
    }
}
