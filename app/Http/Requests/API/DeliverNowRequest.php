<?php

namespace App\Http\Requests\API;

use App\Models\order;
use Illuminate\Foundation\Http\FormRequest;

class DeliverNowRequest extends FormRequest
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

        return [
            'order_id' => "required|exists:{$orderModel},order_id",
            'desc' => 'required|string|max:500',
            'fileUrl' => 'required',
        ];
    }
}
