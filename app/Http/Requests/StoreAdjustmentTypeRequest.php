<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdjustmentTypeRequest extends FormRequest
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
            'name_en' => ['required', 'string', 'max:255', Rule::unique('adjustment_types', 'name_en')->ignore($this->route('adjustment_type'))],
            'name_my' => ['required', 'string', 'max:255', Rule::unique('adjustment_types', 'name_my')->ignore($this->route('adjustment_type'))],
            'category' => ['required', Rule::in(['bonus', 'deduction'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name_en.required' => __('app.validation.adjustment_type_name_required'),
            'name_en.unique' => __('app.validation.adjustment_type_name_unique'),
            'name_my.required' => __('app.validation.adjustment_type_name_required'),
            'name_my.unique' => __('app.validation.adjustment_type_name_unique'),
            'category.required' => __('app.validation.adjustment_category_required'),
            'category.in' => __('app.validation.adjustment_category_invalid'),
        ];
    }
}
