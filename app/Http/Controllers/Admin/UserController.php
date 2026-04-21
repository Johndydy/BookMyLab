<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Only show students, not admins
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'student');
        })->with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('school_email', 'like', "%{$search}%")
                  ->orWhere('school_id_number', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('bookings')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Only show non-admin users
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }

        $bookings = $user->bookings()
            ->with('laboratory', 'approval')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('admin.users.show', compact('user', 'bookings'));
    }
}