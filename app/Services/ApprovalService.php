<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\EquipmentLog;
use Exception;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function approve(Booking $booking, int $adminId, ?string $remarks = null): void
    {
        try {
            DB::transaction(function () use ($booking, $adminId, $remarks) {
                $booking->update(['status' => 'approved']);

                Approval::create([
                    'booking_id' => $booking->booking_id,
                    'admin_id'   => $adminId,
                    'decision'   => 'approved',
                    'remarks'    => $remarks,
                    'decided_at' => now(),
                ]);

                // Create notification for user
                Notification::create([
                    'user_id'    => $booking->user_id,
                    'booking_id' => $booking->booking_id,
                    'message'    => 'Your booking for ' . $booking->laboratory->name . ' has been approved.',
                    'is_read'    => 0,
                ]);

                // Create equipment logs if equipment was requested
                if ($booking->equipment()->exists()) {
                    foreach ($booking->equipment as $bookingEquipment) {
                        EquipmentLog::create([
                            'booking_id'       => $booking->booking_id,
                            'equipment_id'     => $bookingEquipment->equipment_id,
                            'quantity_borrowed' => $bookingEquipment->quantity_requested,
                            'borrowed_at'      => $booking->start_time,
                            'returned_at'      => null,
                            'condition_after'  => null,
                        ]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function reject(Booking $booking, int $adminId, ?string $remarks = null): void
    {
        try {
            DB::transaction(function () use ($booking, $adminId, $remarks) {
                $booking->update(['status' => 'rejected']);

                Approval::create([
                    'booking_id' => $booking->booking_id,
                    'admin_id'   => $adminId,
                    'decision'   => 'rejected',
                    'remarks'    => $remarks,
                    'decided_at' => now(),
                ]);

                // Create notification for user
                Notification::create([
                    'user_id'    => $booking->user_id,
                    'booking_id' => $booking->booking_id,
                    'message'    => 'Your booking for ' . $booking->laboratory->name . ' has been rejected.',
                    'is_read'    => 0,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}

