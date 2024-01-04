<?php

namespace App\Http\Requests\API;

use App\Models\user;
use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
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
        $userModel = get_class(new user());
        return [
            'otherUserId' => "required|exists:{$userModel},user_id",
            'name' =>'nullable',
        ];
    }
}
