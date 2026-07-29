<?php

namespace App\Http\Requests;

use App\Enums\EmploymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
            'department_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'joining_date' => ['required', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'employment_status' => ['required', Rule::enum(EmploymentStatus::class)],
            'wage_amount' => ['required', 'numeric', 'min:0'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'allowed_days_per_month' => ['required', 'integer', 'min:0', 'max:31'],
            'salary_change_reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the employee name.',
            'joining_date.required' => 'Please enter the joining date.',
            'wage_amount.required' => 'Please enter the monthly wage.',
            'salary_change_reason.required' => 'Please provide a reason for the wage change.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $employee = $this->route('employee');

            if (! $employee) {
                return;
            }

            $newWage = (string) $this->input('wage_amount');
            $oldWage = (string) $employee->wage_amount;

            if ($newWage !== $oldWage && blank($this->input('salary_change_reason'))) {
                $validator->errors()->add(
                    'salary_change_reason',
                    'Please provide a reason for the wage change.',
                );
            }
        });
    }
}
