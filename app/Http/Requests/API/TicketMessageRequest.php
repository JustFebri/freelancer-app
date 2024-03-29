<?php

namespace App\Http\Requests\API;

use App\Models\report;
use Illuminate\Foundation\Http\FormRequest;

class TicketMessageRequest extends FormRequest
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
        $reportModel = get_class(new report());
        return [
            'report_id' => "required|exists:{$reportModel},report_id",
            'message' => 'nullable|string',
        ];
    }
}
