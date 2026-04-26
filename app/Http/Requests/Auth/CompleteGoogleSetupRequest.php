<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteGoogleSetupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->user_id;

        return [
            'student_id_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'student_id_number')->ignore($userId, 'user_id'),
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('users', 'username')->ignore($userId, 'user_id'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function messages()
    {
        return [
            'username.regex' => 'The username must contain only lowercase letters, numbers, and underscores.',
        ];
    }
}
