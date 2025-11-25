<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Host;
use App\Services\GoogleCalendarService;

class GoogleCalendarController extends Controller
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function saveTokens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host_id' => 'required|exists:hosts,id',
            'access_token' => 'sometimes|string',
            'refresh_token' => 'sometimes|string',
            'expires_at' => 'sometimes|date',
            'email' => 'sometimes|email',
            'name' => 'sometimes|string',
            'google_id' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $host = Host::find($request->host_id);

            if ($request->has('google_id')) {
                // Unlink other hosts with same googleId
                Host::where('google_id', $request->google_id)
                    ->where('id', '!=', $request->host_id)
                    ->update([
                        'google_id' => null,
                        'google_access_token' => null,
                        'google_refresh_token' => null,
                        'google_token_expiry' => null,
                        'google_calendar_connected' => false,
                        'google_email' => null,
                        'google_name' => null
                    ]);

                $host->google_id = $request->google_id;
            }

            if ($request->has('access_token')) {
                $host->google_access_token = $request->access_token;
            }
            if ($request->has('refresh_token')) {
                $host->google_refresh_token = $request->refresh_token;
            }
            if ($request->has('expires_at')) {
                $host->google_token_expiry = $request->expires_at;
            }
            if ($request->has('email')) {
                $host->google_email = $request->email;
            }
            if ($request->has('name')) {
                $host->google_name = $request->name;
            }

            $host->google_calendar_connected = true;
            $host->save();

            return response()->json([
                'success' => true,
                'message' => 'Google tokens saved for host'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function unlinkAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host_id' => 'required|exists:hosts,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $host = Host::find($request->host_id);

            $host->update([
                'google_id' => null,
                'google_access_token' => null,
                'google_refresh_token' => null,
                'google_token_expiry' => null,
                'google_calendar_connected' => false,
                'google_email' => null,
                'google_name' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Google account unlinked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function createCalendarEvent(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'date' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'vendor_name' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $result = $this->googleCalendarService->createEvent($hostId, $request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create host calendar event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCalendarStatus($hostId)
    {
        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        return response()->json([
            'connected' => (bool) $host->google_calendar_connected,
            'email' => $host->google_email,
            'name' => $host->google_name,
            'token_expiry' => $host->google_token_expiry
        ]);
    }
}
