<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use App\Enums\WorkType;
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
            'work_type' => ['nullable', Rule::enum(WorkType::class)],
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
                $validator->errors()->add('hours_worked', 'Please enter hours or minutes for a present day.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Please choose present or absent.',
        ];
    }
}
