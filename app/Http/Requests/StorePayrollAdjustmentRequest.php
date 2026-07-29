<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollAdjustmentRequest extends FormRequest
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
            'adjustment_type_id' => ['required', 'exists:adjustment_types,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'adjustment_type_id.required' => 'Please select an adjustment type.',
            'amount.required' => 'Please enter the amount.',
            'amount.min' => 'Amount must be greater than zero.',
        ];
    }
}
