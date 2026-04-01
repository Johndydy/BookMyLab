<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function delete(User $user, Booking $booking)
    {
        return $user->user_id === $booking->user_id && $booking->status === 'pending';
    }

    public function view(User $user, Booking $booking)
    {
        return $user->user_id === $booking->user_id;
    }
}
