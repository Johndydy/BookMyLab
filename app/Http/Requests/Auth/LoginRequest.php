<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'school_email' => 'required|email|exists:users,school_email',
            'password'     => 'required|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'school_email.required' => 'School email is required.',
            'school_email.email'    => 'School email must be a valid email address.',
            'school_email.exists'   => 'These credentials do not match our records.',
            'password.required'     => 'Password is required.',
            'password.min'          => 'Password must be at least 6 characters.',
        ];
    }
}
