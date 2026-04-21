<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'school_email'     => 'required|email|ends_with:@school.edu|unique:users,school_email',
            'school_id_number' => 'required|string|max:50|unique:users,school_id_number',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name'       => $validated['first_name'],
            'last_name'        => $validated['last_name'],
            'school_email'     => $validated['school_email'],
            'school_id_number' => $validated['school_id_number'],
            // Never store plain text — always hash passwords
            'password'         => Hash::make($validated['password']),
        ]);

        // Assign student role by default on register
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->assignRole($studentRole);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully.',
            'token'   => $token,
            'user'    => [
                'user_id'          => $user->user_id,
                'full_name'        => $user->full_name,
                'school_email'     => $user->school_email,
                'school_id_number' => $user->school_id_number,
                'roles'            => $user->roles->pluck('name'),
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'school_email' => 'required|email',
            'password'     => 'required|string',
        ]);

        // Rate limiting — max 5 attempts per minute per IP
        $key = 'login-attempt:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many login attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('school_email', $request->school_email)->first();

        // Use Hash::check instead of Auth::attempt to work with custom primary key
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // increment attempt counter, decay in 60s
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        // Revoke all old tokens before issuing a new one
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'token'   => $token,
            'user'    => [
                'user_id'          => $user->user_id,
                'full_name'        => $user->full_name,
                'school_email'     => $user->school_email,
                'school_id_number' => $user->school_id_number,
                'roles'            => $user->roles->pluck('name'),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Only revoke the current token, not all devices
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user_id'          => $user->user_id,
            'full_name'        => $user->full_name,
            'school_email'     => $user->school_email,
            'school_id_number' => $user->school_id_number,
            'roles'            => $user->roles->pluck('name'),
            'permissions'      => $user->roles->flatMap->permissions->pluck('name')->unique()->values(),
        ]);
    }
}