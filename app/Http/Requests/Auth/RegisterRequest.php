<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'                  => 'required|string|max:255',
            'school_email'          => 'required|email|unique:users,school_email',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                    => 'Name is required.',
            'school_email.required'            => 'School email is required.',
            'school_email.email'               => 'School email must be a valid email address.',
            'school_email.unique'              => 'This school email is already registered.',
            'password.required'                => 'Password is required.',
            'password.min'                     => 'Password must be at least 6 characters.',
            'password.confirmed'               => 'Passwords do not match.',
            'password_confirmation.required'   => 'Password confirmation is required.',
        ];
    }
}
