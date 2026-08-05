<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name_en')
                    ->ignore($this->route('department')),
            ],
            'name_my' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name_my')
                    ->ignore($this->route('department')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name_en.required' => __('app.validation.department_name_required'),
            'name_en.unique' => __('app.validation.department_name_unique'),
            'name_my.required' => __('app.validation.department_name_required'),
            'name_my.unique' => __('app.validation.department_name_unique'),
        ];
    }
}
