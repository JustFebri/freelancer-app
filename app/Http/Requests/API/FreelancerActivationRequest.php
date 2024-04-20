<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FreelancerActivationRequest extends FormRequest
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
        return [
            'idCardImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'idCardWithSefieImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'name' => 'required|string|max:200',

            'niknumber' => [
                'required',
                'numeric',
                'digits:16',
                Rule::unique('freelancer', 'identity_number')->where(function ($query) {
                    $query->where('isApproved', '!=', 'rejected');
                }),
            ],
            'nikname' => 'required|string|max:200',
            'nikgender' => 'required|string|max:200',
            'nikaddress' => 'required|string|max:200',
            'description' => 'required|string|max:500',
            'url' => 'nullable|string',
            'languages' => 'required|string',
            'occupations' => 'required|string',
            'skills' => 'required|string|max:500',
            'subcategoryOccupation' => 'required|string|max:500',
        ];
    }
}
