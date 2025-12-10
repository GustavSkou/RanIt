<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function showProfile(User $user)
    {
        if (Auth::user() != $user) {
            return back();
        }
        return view('profile')->with('user', $user);
    }

    function showEdit(User $user)
    {
        if (Auth::user() != $user) {
            return back();
        }
    }
}
