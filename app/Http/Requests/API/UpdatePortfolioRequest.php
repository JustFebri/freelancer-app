<?php

namespace App\Http\Requests\API;

use App\Models\portfolio;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
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
        $portfolioModel = get_class(new portfolio());

        return [
            'title' => 'required|string|max:255',
            'desc' => 'required|string|max:500',
            'portfolioId' => "required|exists:{$portfolioModel},portfolio_id",
            'updateImage' => 'required|string|max:255|in:true,false',
        ];
    }
}
