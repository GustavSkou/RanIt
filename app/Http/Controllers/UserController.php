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

    function index(Request $request)
    {
        $validated = $request->validate([
            'searchInput' => 'required|string'
        ]);

        $search = $validated['searchInput'];

        $candidates = User::query()
            ->where('name', 'like', '%' . $search . '%')
            ->take(50)
            ->get();

        $users = $candidates->sortByDesc(function (User $user) use ($search) {
            return similar_text(strtolower($user->name), strtolower($search));
        })->values();

        return view('users')->with('users', $users);
    }
}
