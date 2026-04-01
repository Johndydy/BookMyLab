<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Booking;
use Exception;

class ApprovalService
{
    public function approve(Booking $booking, int $adminId, ?string $remarks = null): void
    {
        try {
            $booking->update(['status' => 'approved']);

            Approval::create([
                'booking_id' => $booking->booking_id,
                'admin_id'   => $adminId,
                'decision'   => 'approved',
                'remarks'    => $remarks,
                'decided_at' => now(),
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function reject(Booking $booking, int $adminId, ?string $remarks = null): void
    {
        try {
            $booking->update(['status' => 'rejected']);

            Approval::create([
                'booking_id' => $booking->booking_id,
                'admin_id'   => $adminId,
                'decision'   => 'rejected',
                'remarks'    => $remarks,
                'decided_at' => now(),
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
