<?php

namespace App\Http\Requests\User;

use App\Models\Equipment;
use Carbon\Carbon;
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
            'purpose'               => 'required|in:Study,Presentation,Defense,Research',
            'number_of_persons'     => 'required|integer|min:1',
            'start_time'            => 'required|date_format:Y-m-d H:i|after:now',
            'end_time'              => 'required|date_format:Y-m-d H:i|after:start_time',
            'equipment_ids'         => 'nullable|array',
            'equipment_ids.*'       => 'exists:equipment,equipment_id',
            'equipment_quantities'  => 'nullable|array',
            'equipment_quantities.*' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        // Validate that equipment_quantities are only provided when equipment_ids are provided
        $validator->after(function ($validator) {
            $equipmentIds = $this->input('equipment_ids', []);
            $equipmentQuantities = $this->input('equipment_quantities', []);

            // Check Laboratory Capacity
            if ($this->input('laboratory_id') && $this->input('number_of_persons')) {
                $lab = \App\Models\Laboratory::find($this->input('laboratory_id'));
                if ($lab && $this->input('number_of_persons') > $lab->capacity) {
                    $validator->errors()->add('number_of_persons', "The number of persons exceeds the laboratory's maximum capacity of {$lab->capacity}.");
                }
            }

            // Filter out empty values
            $equipmentIds = array_filter($equipmentIds);
            $equipmentQuantities = array_filter($equipmentQuantities, function ($val) {
                return $val !== null && $val !== '';
            });

            // If quantities are provided without IDs, it's an error
            if (!empty($equipmentQuantities) && empty($equipmentIds)) {
                $validator->errors()->add('equipment_ids', 'Please select equipment before specifying quantities.');
                return;
            }

            // Validate that requested quantities don't exceed available quantities
            if (!empty($equipmentIds)) {
                foreach ($equipmentIds as $key => $equipmentId) {
                    if (!isset($equipmentQuantities[$key])) {
                        continue;
                    }

                    $quantity = (int)$equipmentQuantities[$key];
                    if ($quantity <= 0) {
                        continue;
                    }

                    $equipment = Equipment::find($equipmentId);
                    if (!$equipment) {
                        continue;
                    }

                    if ($quantity > $equipment->quantity) {
                        $validator->errors()->add(
                            'equipment_quantities',
                            "Cannot request {$quantity} units of {$equipment->name}. Only {$equipment->quantity} available."
                        );
                    }
                }
            }

            // Validate booking days and hours
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');

            if ($startTime) {
                try {
                    $start = Carbon::createFromFormat('Y-m-d H:i', $startTime);
                    $startHour = (int) $start->format('H');
                    $startMinute = (int) $start->format('i');
                    $startDay = $start->dayOfWeek; // 0=Sunday, 6=Saturday

                    // Block Sunday entirely
                    if ($startDay === Carbon::SUNDAY) {
                        $validator->errors()->add('start_time', 'Bookings are not available on Sundays.');
                    }
                    // Saturday: 7 AM – 12 PM only
                    elseif ($startDay === Carbon::SATURDAY) {
                        if ($startHour < 7 || $startHour >= 12) {
                            $validator->errors()->add('start_time', 'Saturday bookings can only start between 7:00 AM and 12:00 PM.');
                        }
                    }
                    // Weekdays: 7 AM – 6 PM
                    else {
                        if ($startHour < 7 || $startHour >= 18) {
                            $validator->errors()->add('start_time', 'Bookings can only start between 7:00 AM and 6:00 PM.');
                        }
                    }
                } catch (\Exception $e) {
                    // Format error will be caught by date_format rule
                }
            }

            if ($endTime) {
                try {
                    $end = Carbon::createFromFormat('Y-m-d H:i', $endTime);
                    $endHour = (int) $end->format('H');
                    $endMinute = (int) $end->format('i');
                    $endDay = $end->dayOfWeek;

                    // Block Sunday entirely
                    if ($endDay === Carbon::SUNDAY) {
                        $validator->errors()->add('end_time', 'Bookings cannot extend into Sunday.');
                    }
                    // Saturday: must end by 12 PM
                    elseif ($endDay === Carbon::SATURDAY) {
                        if ($endHour > 12 || ($endHour === 12 && $endMinute > 0)) {
                            $validator->errors()->add('end_time', 'Saturday bookings must end by 12:00 PM.');
                        }
                    }
                    // Weekdays: must end by 6 PM
                    else {
                        if ($endHour > 18 || ($endHour === 18 && $endMinute > 0)) {
                            $validator->errors()->add('end_time', 'Bookings must end by 6:00 PM.');
                        }
                    }
                } catch (\Exception $e) {
                    // Format error will be caught by date_format rule
                }
            }
        });
    }

    public function messages()
    {
        return [
            'laboratory_id.required'        => 'Laboratory is required.',
            'laboratory_id.exists'          => 'Selected laboratory does not exist.',
            'purpose.required'              => 'Purpose is required.',
            'purpose.in'                    => 'Please select a valid purpose from the list.',
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

