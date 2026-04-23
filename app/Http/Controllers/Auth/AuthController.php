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
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Auth\CompleteGoogleSetupRequest;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'school_email'     => 'required|email|ends_with:@urios.edu.ph|unique:users,school_email',
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
        $permissions = $user->roles->flatMap->permissions->pluck('name')->unique()->values();
        $isAdmin = false; // New registrations are never admin

        return response()->json([
            'message' => 'Registered successfully.',
            'token'   => $token,
            'user'    => [
                'user_id'          => $user->user_id,
                'full_name'        => $user->full_name,
                'school_email'     => $user->school_email,
                'school_id_number' => $user->school_id_number,
                'roles'            => $user->roles->pluck('name'),
                'permissions'      => $permissions,
                'is_admin'         => $isAdmin,
                'dashboard_url'    => '/dashboard',
            ],
        ], 201);
    }

    /**
     * API Login - returns JSON with token for mobile/SPA clients
     */
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

        // Create new token (keep existing tokens for multi-device support)
        $token = $user->createToken('auth_token')->plainTextToken;

        $isAdmin = $user->roles->contains('name', 'administrator');
        $permissions = $user->roles->flatMap->permissions->pluck('name')->unique()->values();

        return response()->json([
            'message' => 'Logged in successfully.',
            'token'   => $token,
            'user'    => [
                'user_id'          => $user->user_id,
                'full_name'        => $user->full_name,
                'school_email'     => $user->school_email,
                'school_id_number' => $user->school_id_number,
                'roles'            => $user->roles->pluck('name'),
                'permissions'      => $permissions,
                'is_admin'         => $isAdmin,
                'dashboard_url'    => $isAdmin ? '/admin/dashboard' : '/dashboard',
            ],
        ]);
    }

    /**
     * Web Login - handles form submission with redirect
     */
    public function loginWeb(Request $request)
    {
        $request->validate([
            'school_email' => 'required|email',
            'password'     => 'required|string',
        ]);

        // Rate limiting — max 5 attempts per minute per IP
        $key = 'login-attempt:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['message' => "Too many login attempts. Try again in {$seconds} seconds."]);
        }

        $user = User::where('school_email', $request->school_email)->first();

        // Use Hash::check instead of Auth::attempt to work with custom primary key
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            return back()->withErrors(['message' => 'Invalid credentials.']);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        // Create session-based authentication
        Auth::login($user, $request->boolean('remember'));

        // Create new token (keep existing tokens for multi-device support)
        $token = $user->createToken('auth_token')->plainTextToken;

        $isAdmin = $user->roles->contains('name', 'administrator');
        $redirectUrl = $isAdmin ? '/admin/dashboard' : '/dashboard';

        return redirect()->intended($redirectUrl);
    }

    /**
     * API Logout - revokes current Sanctum token
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Only revoke the current token for multi-device support
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Web Logout - destroys session
     */
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
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

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Validate domain
            if (!Str::endsWith($googleUser->email, '@urios.edu.ph')) {
                return redirect()->route('login')->with('error', 'Only @urios.edu.ph school emails are allowed.');
            }

            $user = User::where('google_id', $googleUser->id)->first();
            $isNewGoogleUser = false;

            if (!$user) {
                $user = User::where('school_email', $googleUser->email)->first();

                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                    ]);
                } else {
                    // Split full name from Google into first and last name
                    $nameParts = explode(' ', $googleUser->name, 2);
                    $firstName = $nameParts[0];
                    $lastName = $nameParts[1] ?? '';

                    $user = User::create([
                        'first_name'   => $firstName,
                        'last_name'    => $lastName,
                        'school_email' => $googleUser->email,
                        'google_id'    => $googleUser->id,
                        'google_token' => $googleUser->token,
                        'password'     => Hash::make(Str::random(24)),
                    ]);

                    // Assign student role by default
                    $studentRole = Role::where('name', 'student')->first();
                    if ($studentRole) {
                        $user->assignRole($studentRole);
                    }

                    $isNewGoogleUser = true;
                }
            } else {
                $user->update([
                    'google_token' => $googleUser->token,
                ]);
            }

            Auth::login($user);

            if ($isNewGoogleUser || !$user->username) {
                session(['show_google_setup_modal' => true]);
                return redirect()->route('user.dashboard')->with('success', 'Welcome! Please complete your account setup.');
            }

            $isAdmin = $user->isAdmin();
            $redirectUrl = $isAdmin ? '/admin/dashboard' : '/dashboard';

            return redirect()->intended($redirectUrl)->with('success', 'Logged in with Google successfully!');
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    public function completeGoogleSetup(CompleteGoogleSetupRequest $request)
    {
        $request->user()->update([
            'school_id_number'     => $request->school_id_number,
            'username'             => $request->username,
            'password'             => Hash::make($request->password),
            'profile_completed_at' => $request->user()->profile_completed_at ?? now(),
        ]);

        session()->forget('show_google_setup_modal');

        return redirect()->route('user.dashboard')->with('success', 'Account setup completed successfully!');
    }
}