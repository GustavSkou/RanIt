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

        if (Auth($validated)) {
            $request->session()->regenerate();
            return redirect()->route('activity.index');
        }
    }

    function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email',
            'password' => 'required|string|min:5|confirmed'
        ]);

        $user = User::create($validated);
        Auth::login($user);
        redirect()->route('activity.index');
    }

    function ShowLogin()
    {
        return view('login');
    }

    function ShowRegister()
    {
        return view('register');
    }
}
