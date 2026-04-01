<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('school_email', 'like', '%' . $request->search . '%');
        }

        $users = $query->withCount('bookings')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (!$user->isAdmin()) {
            $bookings = $user->bookings()
                ->orderBy('start_time', 'desc')
                ->paginate(10);
            return view('admin.users.show', compact('user', 'bookings'));
        }

        return redirect()->route('admin.users.index')
            ->with('error', 'User not found.');
    }
}
