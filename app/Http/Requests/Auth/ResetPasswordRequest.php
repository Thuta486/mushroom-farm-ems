<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('app.validation.reset_email_required'),
            'email.email' => __('app.validation.reset_email_valid'),
            'password.required' => __('app.validation.reset_password_required'),
            'password.min' => __('app.validation.reset_password_min'),
            'password.confirmed' => __('app.validation.reset_password_confirmed'),
        ];
    }
}
