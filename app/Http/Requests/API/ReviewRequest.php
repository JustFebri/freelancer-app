<?php

namespace App\Http\Requests\API;

use App\Models\freelancer;
use App\Models\order;
use App\Models\service;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
        $orderModel = get_class(new order());
        $freelancerModel = get_class(new freelancer());
        $serviceModel = get_class(new service());

        return [
            'order_id' => "required|string|max:255|exists:{$orderModel},order_id",
            'freelancer_id' => "required|exists:{$freelancerModel},freelancer_id",
            'rating' => 'required|numeric|min:0|max:5',
            'comment' => 'required|string|max:500',
            'service_id' => "required|exists:{$serviceModel},service_id",
            'broadcast' => 'nullable|in:yes,no',
        ];
    }
}
