<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kudosList;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;

class KudosController extends Controller
{
    public function Kudos(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|integer'
        ]);
        $validated['user_id'] = Auth::user()->id;

        if (0 < kudosList::where('user_id', $validated['user_id'])->where('activity_id', $validated['activity_id'])->count()) {
            return $this->RemoveKudos($validated);
        } else {
            return $this->InsertKudos($validated);
        }
    }

    private function InsertKudos($kudos)
    {
        $response = kudosList::insert($kudos);
        $activity = Activity::find($kudos['activity_id']);

        $response = response()->json([
            'success' => true,
            'kudos_count' => $activity->kudos_count(),
            'liked' => $activity->kudosByAuth()
        ]);

        // Log::info($response);

        return $response;
    }

    private function RemoveKudos($kudos)
    {
        kudosList::where('user_id', $kudos['user_id'])->where('activity_id', $kudos['activity_id'])->delete();
        $activity = Activity::find($kudos['activity_id']);
        $response = response()->json([
            'success' => true,
            'kudos_count' => $activity->kudos_count(),
            'liked' => $activity->kudosByAuth()
        ]);

        // Log::info($response);

        return $response;
    }
}
