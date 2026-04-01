<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $deptId = $this->route('department');
        $uniqueRule = $deptId ? "unique:departments,name,{$deptId},department_id" : 'unique:departments,name';

        return [
            'name'     => "required|string|max:255|{$uniqueRule}",
            'building' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required'    => 'Department name is required.',
            'name.unique'      => 'This department name already exists.',
            'name.max'         => 'Department name cannot exceed 255 characters.',
            'building.required' => 'Building is required.',
            'building.max'     => 'Building cannot exceed 255 characters.',
        ];
    }
}
