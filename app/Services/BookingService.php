<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingEquipment;
use App\Models\Laboratory;
use Exception;

class BookingService
{
    public function create(array $data, int $userId): Booking
    {
        try {
            if ($this->hasConflict($data['laboratory_id'], $data['start_time'], $data['end_time'])) {
                throw new Exception('The selected time slot is already booked for this laboratory.');
            }

            $booking = Booking::create([
                'user_id'       => $userId,
                'laboratory_id' => $data['laboratory_id'],
                'purpose'       => $data['purpose'],
                'start_time'    => $data['start_time'],
                'end_time'      => $data['end_time'],
                'status'        => 'pending',
            ]);

            if (isset($data['equipment_ids']) && is_array($data['equipment_ids'])) {
                foreach ($data['equipment_ids'] as $key => $equipmentId) {
                    BookingEquipment::create([
                        'booking_id'         => $booking->booking_id,
                        'equipment_id'       => $equipmentId,
                        'quantity_requested' => $data['equipment_quantities'][$key] ?? 1,
                    ]);
                }
            }

            return $booking;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancel(Booking $booking): void
    {
        $booking->update(['status' => 'cancelled']);
    }

    public function hasConflict(int $labId, string $startTime, string $endTime): bool
    {
        return Booking::where('laboratory_id', $labId)
            ->where('status', 'approved')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->exists();
    }
}
