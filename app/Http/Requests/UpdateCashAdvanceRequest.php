<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashAdvanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'advance_type_id' => ['required', 'exists:advance_types,id'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => __('app.validation.cash_advance_employee_required'),
            'advance_type_id.required' => __('app.validation.advance_type_required'),
            'date.required' => __('app.validation.cash_advance_date_required'),
            'amount.required' => __('app.validation.cash_advance_amount_required'),
            'amount.min' => __('app.validation.cash_advance_amount_min'),
        ];
    }
}
