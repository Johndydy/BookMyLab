<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'laboratory_id'         => 'required|exists:laboratories,laboratory_id',
            'purpose'               => 'required|string|max:500',
            'start_time'            => 'required|date_format:Y-m-d H:i|after:now',
            'end_time'              => 'required|date_format:Y-m-d H:i|after:start_time',
            'equipment_ids'         => 'nullable|array',
            'equipment_ids.*'       => 'exists:equipment,equipment_id',
            'equipment_quantities'  => 'nullable|array',
            'equipment_quantities.*' => 'integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'laboratory_id.required'        => 'Laboratory is required.',
            'laboratory_id.exists'          => 'Selected laboratory does not exist.',
            'purpose.required'              => 'Purpose is required.',
            'purpose.max'                   => 'Purpose cannot exceed 500 characters.',
            'start_time.required'           => 'Start time is required.',
            'start_time.date_format'        => 'Start time must be in format: YYYY-MM-DD HH:mm.',
            'start_time.after'              => 'Start time must be in the future.',
            'end_time.required'             => 'End time is required.',
            'end_time.date_format'          => 'End time must be in format: YYYY-MM-DD HH:mm.',
            'end_time.after'                => 'End time must be after start time.',
            'equipment_ids.*.exists'        => 'Selected equipment does not exist.',
            'equipment_quantities.*.integer' => 'Equipment quantity must be a number.',
            'equipment_quantities.*.min'    => 'Equipment quantity must be at least 1.',
        ];
    }
}
