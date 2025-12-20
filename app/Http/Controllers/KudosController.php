<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kudosList;

class KudosController extends Controller
{
    public function Kudos(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'activity_id' => 'required|integer'
        ]);

        if (0 < kudosList::where('user_id', $validated['user_id'])->where('activity_id', $validated['activity_id'])->count()) {
            return back();
        } else {
            kudosList::insert($validated);
            return back();
        }
    }
}
