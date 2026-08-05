<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAttendanceRequest extends FormRequest
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
            'status' => ['required', Rule::enum(AttendanceStatus::class)],
            'hours_worked' => ['required', 'integer', 'min:0', 'max:24'],
            'minutes_worked' => ['required', 'integer', 'min:0', 'max:59'],
            // 'work_type' removed: handled in migration
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('status') !== AttendanceStatus::Present->value) {
                return;
            }

            $hours = (int) $this->input('hours_worked', 0);
            $minutes = (int) $this->input('minutes_worked', 0);

            if ($hours === 0 && $minutes === 0) {
                $validator->errors()->add('hours_worked', __('app.validation.attendance_present_hours_or_minutes_required'));
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => __('app.validation.attendance_status_required'),
        ];
    }
}
