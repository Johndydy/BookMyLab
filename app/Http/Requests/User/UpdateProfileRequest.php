<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->user_id;

        return [
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'school_id_number' => ['required', 'string', 'max:50', Rule::unique('users', 'school_id_number')->ignore($userId, 'user_id')],
            'username'         => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', Rule::unique('users', 'username')->ignore($userId, 'user_id')],
            'phone_number'     => 'nullable|string|max:20',
            'student_id'       => 'nullable|string|max:50',
            'department_name'  => 'nullable|string|max:100',
            'course'           => 'nullable|string|max:100',
            'year_level'       => 'nullable|string|max:20',
            'bio'              => 'nullable|string|max:500',
        ];
    }
}
