<?php

namespace App\Http\Requests\API;

use App\Models\freelancer;
use App\Models\service_package;
use App\Models\user;
use Illuminate\Foundation\Http\FormRequest;

class MidtransPayment extends FormRequest
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
        $userModel = get_class(new user());
        $packageModel = get_class(new service_package());
        $freelancerModel = get_class(new freelancer());

        return [
            'name' => 'required|string|max:255',
            'email' => "required|exists:{$userModel},email",
            'package_id' => "required|exists:{$packageModel},package_id",
            'price' => 'required|numeric',
            'service_name' => 'required|string|max:255',
            'package_name' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            'merchant_name' => 'required|string|max:255',
            'seller_id' => "required|exists:{$freelancerModel},freelancer_id",
            'onsite_date' => 'nullable|date',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'loc' => 'nullable|string|max:255',
        ];
    }
}
