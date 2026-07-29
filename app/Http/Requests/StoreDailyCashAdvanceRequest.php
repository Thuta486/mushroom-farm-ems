<?php

namespace App\Http\Requests;

use App\Enums\EmploymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDailyCashAdvanceRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'advances' => ['array'],
            'advances.*.employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where('employment_status', EmploymentStatus::Active->value),
            ],
            'advances.*.advance_type_id' => ['nullable', 'exists:advance_types,id'],
            'advances.*.amount' => ['nullable', 'numeric', 'min:0'],
            'advances.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('advances', []) as $index => $row) {
                $amount = (float) ($row['amount'] ?? 0);

                if ($amount > 0 && empty($row['advance_type_id'])) {
                    $validator->errors()->add(
                        "advances.{$index}.advance_type_id",
                        'Please select an advance type for this amount.',
                    );
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Please choose the date.',
        ];
    }
}