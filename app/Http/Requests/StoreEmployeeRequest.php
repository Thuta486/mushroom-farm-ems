<?php

namespace App\Http\Requests;

use App\Enums\EmploymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
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
            'wage_amount.min' => 'Wage cannot be negative.',
        ];
    }
}
