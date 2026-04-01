<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LaboratoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $labId = $this->route('laboratory');
        $uniqueRule = $labId ? "unique:laboratories,name,{$labId},laboratory_id" : 'unique:laboratories,name';

        return [
            'department_id' => 'required|exists:departments,department_id',
            'name'          => "required|string|max:255|{$uniqueRule}",
            'location'      => 'required|string|max:255',
            'capacity'      => 'required|integer|min:1',
            'status'        => 'required|in:available,maintenance',
        ];
    }

    public function messages()
    {
        return [
            'department_id.required' => 'Department is required.',
            'department_id.exists'   => 'Selected department does not exist.',
            'name.required'          => 'Laboratory name is required.',
            'name.unique'            => 'This laboratory name already exists.',
            'name.max'               => 'Laboratory name cannot exceed 255 characters.',
            'location.required'      => 'Location is required.',
            'location.max'           => 'Location cannot exceed 255 characters.',
            'capacity.required'      => 'Capacity is required.',
            'capacity.integer'       => 'Capacity must be a number.',
            'capacity.min'           => 'Capacity must be at least 1.',
            'status.required'        => 'Status is required.',
            'status.in'              => 'Status must be either available or maintenance.',
        ];
    }
}
