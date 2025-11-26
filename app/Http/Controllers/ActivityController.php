<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\point;
use FFI\Exception;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Session\FileSessionHandler;
use SebastianBergmann\CodeCoverage\Util\Xml;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Environment\Console;

class ActivityController extends Controller
{
    public function ShowUpload()
    {
        return view('file_upload');
    }

    public function Upload(Request $request)
    {
        try {
            Log::info('Upload method started: Request data', [
                'hasFile' => $request->hasFile('fileUpload'),
                'allFiles' => $request->allFiles(),
                'all' => $request->all()
            ]);

            $validated = $request->validate([
                'file' => 'required|file'
            ]);
            $file = $validated['file'];

            Log::info('File validated successfully');

            $contentString = $file->get();
            $xml = simplexml_load_string($contentString);
            $trackName = isset($xml->trk->name) ? (string)$xml->trk->name : 'Unnamed Activity';

            $activity = Activity::create([
                'name' => $trackName,
                'user_id' => Auth::user()->id
            ]);
            Log::info('Activity created', ['activity_id' => $activity->id, 'name' => $activity->name]);

            foreach ($xml->trk as $track) {
                foreach ($track->trkseg as $segment) {
                    foreach ($segment->trkpt as $point) {
                        $latitude = (float) $point['lat'];
                        $longitude = (float) $point['lon'];
                        $elevation = (float) $point->ele;
                        $time = (string) $point->time;

                        point::create([
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'elevation' => $elevation,
                            'timestamp' => $time,
                            'activity_id' => $activity->id
                        ]);
                    }
                }
            }

            Log::info('Upload completed successfully', [
                'activity_id' => $activity->id,
            ]);

            return redirect()->route('show.upload')->with('success', 'File uploaded successfully');
        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }
}
