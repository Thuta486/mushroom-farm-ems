<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use App\Enums\EmploymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDailyAttendanceRequest extends FormRequest
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
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where('employment_status', EmploymentStatus::Active->value),
            ],
            'attendances.*.status' => ['required', Rule::enum(AttendanceStatus::class)],
            'attendances.*.hours_worked' => ['nullable', 'required_if:attendances.*.status,present', 'integer', 'min:0', 'max:24'],
            'attendances.*.minutes_worked' => ['nullable', 'required_if:attendances.*.status,present', 'integer', 'min:0', 'max:59'],
            // 'work_type' removed: attendances.*.work_type
            'attendances.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('attendances', []) as $index => $row) {
                if (($row['status'] ?? null) !== AttendanceStatus::Present->value) {
                    continue;
                }

                $hours = (int) ($row['hours_worked'] ?? 0);
                $minutes = (int) ($row['minutes_worked'] ?? 0);

                if ($hours === 0 && $minutes === 0) {
                    $validator->errors()->add(
                        "attendances.{$index}.hours_worked",
                        'Please enter hours or minutes for present employees.',
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
            'date.required' => 'Please choose the attendance date.',
            'attendances.required' => 'No employees were found to save.',
        ];
    }
}
