<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'laboratory_id' => 'required|exists:laboratories,laboratory_id',
            'name'          => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'condition'     => 'required|in:good,damaged,under repair',
        ];
    }

    public function messages()
    {
        return [
            'laboratory_id.required' => 'Laboratory is required.',
            'laboratory_id.exists'   => 'Selected laboratory does not exist.',
            'name.required'          => 'Equipment name is required.',
            'name.max'               => 'Equipment name cannot exceed 255 characters.',
            'quantity.required'      => 'Quantity is required.',
            'quantity.integer'       => 'Quantity must be a number.',
            'quantity.min'           => 'Quantity must be at least 1.',
            'condition.required'     => 'Condition is required.',
            'condition.in'           => 'Condition must be good, damaged, or under repair.',
        ];
    }
}
