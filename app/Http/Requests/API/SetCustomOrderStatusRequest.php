<?php

namespace App\Http\Requests\API;

use App\Models\Chat;
use App\Models\custom_orders;
use Illuminate\Foundation\Http\FormRequest;

class SetCustomOrderStatusRequest extends FormRequest
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
        $chatRoomModel = get_class(new Chat());

        return [
            'custom_id' => "required|exists:{$customModel},custom_id",
            'status' => "required|string|max:255",
            'chatRoom_id' => "required|exists:{$chatRoomModel},chatRoom_id",
        ];
    }
}
