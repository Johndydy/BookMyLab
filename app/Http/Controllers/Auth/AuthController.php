<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('school_email', $request->school_email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            return redirect()->intended(route('user.dashboard'))->with('success', 'Login successful!');
        }

        return back()->withInput($request->only('school_email'))->with('error', 'Invalid credentials.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name'         => $request->name,
                'school_email' => $request->school_email,
                'password'     => Hash::make($request->password),
                'role'         => 'user',
            ]);

            Auth::login($user);
            return redirect()->route('user.dashboard')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            return back()->withInput($request->only('name', 'school_email'))->with('error', 'Registration failed. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
