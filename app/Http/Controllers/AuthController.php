<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required | email',
            'password' => 'required | string'
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email',
            'password' => 'required|string|min:1|confirmed',
            'profile-picture-path' => 'sometimes|string'
        ]);

        $user = User::create($validated);
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('show.login');
    }

    function ShowLogin()
    {
        return view('auth.login');
    }

    function ShowRegister()
    {
        return view('auth.register');
    }
}
