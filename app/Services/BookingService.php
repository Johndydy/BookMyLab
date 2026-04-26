<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingEquipment;
use App\Models\Laboratory;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(array $data, int $userId): Booking
    {
        try {
            return DB::transaction(function () use ($data, $userId) {
                if ($this->hasConflict($data['laboratory_id'], $data['start_time'], $data['end_time'])) {
                    throw new Exception('The selected time slot is already booked for this laboratory.');
                }

                $booking = Booking::create([
                    'user_id'           => $userId,
                    'laboratory_id'     => $data['laboratory_id'],
                    'purpose'           => $data['purpose'],
                    'number_of_persons' => $data['number_of_persons'],
                    'start_time'        => $data['start_time'],
                    'end_time'          => $data['end_time'],
                    'status'            => 'pending',
                ]);

                // Only insert equipment if actually selected and quantities are provided
                $equipmentIds = $data['equipment_ids'] ?? [];
                $equipmentQuantities = $data['equipment_quantities'] ?? [];

                // Filter out empty/null values
                $equipmentIds = array_filter($equipmentIds);
                $equipmentQuantities = array_filter($equipmentQuantities, function ($val) {
                    return $val !== null && $val !== '';
                });

                if (!empty($equipmentIds) && !empty($equipmentQuantities)) {
                    foreach ($equipmentIds as $key => $equipmentId) {
                        if (isset($equipmentQuantities[$key]) && $equipmentQuantities[$key] > 0) {
                            BookingEquipment::create([
                                'booking_id'         => $booking->booking_id,
                                'equipment_id'       => $equipmentId,
                                'quantity_requested' => (int)$equipmentQuantities[$key],
                            ]);
                        }
                    }
                }

                return $booking;
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancel(Booking $booking): void
    {
        try {
            $booking->update(['status' => 'cancelled']);
        } catch (Exception $e) {
            throw $e;
        }
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
