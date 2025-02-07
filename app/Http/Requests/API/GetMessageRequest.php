<?php

namespace App\Http\Requests\API;

use App\Models\Chat;
use Illuminate\Foundation\Http\FormRequest;

class GetMessageRequest extends FormRequest
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

        return [
            'chatRoom_id' => "required|exists:{$chatModel},chatRoom_id",
        ];
    }
}
