<?php

namespace App\Http\Requests\API;

use App\Models\Chat;
use App\Models\custom_orders;
use App\Models\service;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $chatModel = get_class(new Chat());
        $serviceModel = get_class(new service());
        $customModel = get_class(new custom_orders());

        return [
            'chatRoom_id' => "required|exists:{$chatModel},chatRoom_id",
            'service_id' => "exists:{$serviceModel},service_id",
            'custom_id' => "exists:{$customModel},custom_id",
            'message' => 'string|nullable',
        ];
    }
}
