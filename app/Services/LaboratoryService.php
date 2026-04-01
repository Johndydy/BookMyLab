<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Laboratory;
use Exception;

class LaboratoryService
{
    public function setMaintenance(Laboratory $lab): void
    {
        try {
            $lab->update(['status' => 'maintenance']);

            Booking::where('laboratory_id', $lab->laboratory_id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function setAvailable(Laboratory $lab): void
    {
        $lab->update(['status' => 'available']);
    }
}
