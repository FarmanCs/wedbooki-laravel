<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Host\Host;
use Illuminate\Support\Facades\Http;

class GoogleCalendarController extends Controller
{
    /**
     * SAVE HOST GOOGLE TOKENS (Sanctum Auth)
     */
    public function saveHostGoogleTokens(Request $request)
    {
        $hostId = auth()->id();

        if (!$hostId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'google_id'      => 'nullable|string',
            'access_token'   => 'nullable|string',
            'refresh_token'  => 'nullable|string',
            'expires_at'     => 'nullable|date',
            'email'          => 'nullable|email',
            'name'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $host = Host::findOrFail($hostId);

            // Unlink same google_id from other hosts (same as Node.js version)
            if ($request->google_id) {
                Host::where('google_id', $request->google_id)
                    ->where('id', '!=', $hostId)
                    ->update([
                        'google_id' => null,
                        'google_access_token' => null,
                        'google_refresh_token' => null,
                        'google_token_expiry' => null,
                        'google_calendar_connected' => false,
                        'google_email' => null,
                        'google_name' => null,
                    ]);

                $host->google_id = $request->google_id;
            }

            // Save tokens
            if ($request->access_token) {
                $host->google_access_token = $request->access_token;
            }

            if ($request->refresh_token) {
                $host->google_refresh_token = $request->refresh_token;
            }

            if ($request->expires_at) {
                $host->google_token_expiry = $request->expires_at;
            }

            if ($request->email) {
                $host->google_email = $request->email;
            }

            if ($request->name) {
                $host->google_name = $request->name;
            }

            $host->google_calendar_connected = true;
            $host->save();

            return response()->json([
                'success' => true,
                'message' => 'Google tokens saved for host'
            ]);

        } catch (\Exception $e) {
            \Log::error("saveHostGoogleTokens error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * UNLINK GOOGLE ACCOUNT (Sanctum)
     */
    public function unlinkHostGoogleAccount()
    {
        $hostId = auth()->id();

        if (!$hostId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $host = Host::findOrFail($hostId);

            $host->update([
                'google_id' => null,
                'google_access_token' => null,
                'google_refresh_token' => null,
                'google_token_expiry' => null,
                'google_calendar_connected' => false,
                'google_email' => null,
                'google_name' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Google account unlinked successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * CREATE GOOGLE CALENDAR EVENT (Sanctum Host)
     */
    public function createHostGoogleCalendarEvent(Request $request)
    {
        $hostId = auth()->id();

        if (!$hostId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'nullable|string',
            'description' => 'nullable|string',
            'date'        => 'required|date',
            'start_time'  => 'required|string',
            'end_time'    => 'required|string',
            'vendor_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $host = Host::find($hostId);

            if (!$host->google_access_token && !$host->google_refresh_token) {
                return response()->json(['message' => 'Google account not connected'], 400);
            }

            $accessToken = $host->google_access_token;
            $now = now();

            // Token expired → refresh
            if (!$accessToken || ($host->google_token_expiry && $host->google_token_expiry <= $now)) {
                if (!$host->google_refresh_token) {
                    return response()->json(['message' => 'No refresh token; reconnect Google'], 400);
                }

                $tokenResponse = Http::asForm()->post(
                    "https://oauth2.googleapis.com/token",
                    [
                        'client_id' => env('GOOGLE_CLIENT_ID'),
                        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                        'refresh_token' => $host->google_refresh_token,
                        'grant_type' => 'refresh_token',
                    ]
                );

                if (!$tokenResponse->successful()) {
                    return response()->json([
                        'message' => 'Failed to refresh token',
                        'error' => $tokenResponse->json()
                    ], 400);
                }

                $json = $tokenResponse->json();
                $accessToken = $json['access_token'];

                if (isset($json['expires_in'])) {
                    $host->google_token_expiry = now()->addSeconds($json['expires_in']);
                }

                $host->google_access_token = $accessToken;
                $host->save();
            }

            $start = $request->date . 'T' . $request->start_time . ':00';
            $end   = $request->date . 'T' . $request->end_time . ':00';

            $event = [
                'summary' => $request->title ?? "Booking - " . ($request->vendor_name ?? "Vendor"),
                'description' => $request->description ?? "Vendor: " . ($request->vendor_name ?? "N/A"),
                'start' => ['dateTime' => $start],
                'end' => ['dateTime' => $end],
                'reminders' => ['useDefault' => true],
            ];

            $calendar = Http::withToken($accessToken)->post(
                "https://www.googleapis.com/calendar/v3/calendars/primary/events",
                $event
            );

            if (!$calendar->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create event',
                    'error' => $calendar->json()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'event' => $calendar->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET GOOGLE CALENDAR CONNECTION STATUS (Sanctum)
     */
    public function getHostGoogleCalendarStatus()
    {
        $hostId = auth()->id();

        if (!$hostId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $host = Host::select(
            'google_calendar_connected',
            'google_email',
            'google_name',
            'google_token_expiry'
        )->find($hostId);

        return response()->json([
            'connected' => (bool) $host->google_calendar_connected,
            'email' => $host->google_email,
            'name' => $host->google_name,
            'token_expiry' => $host->google_token_expiry,
        ]);
    }

    /**
     * ADMIN — TRANSFER GOOGLE ACCOUNT BETWEEN HOSTS
     */
    public function transferHostGoogleAccount(Request $request)
    {
        $admin = auth()->user();

        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['message' => 'Admin role required'], 403);
        }

        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'target_host_id' => 'required|exists:hosts,id',
            'transfer_tokens' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $source = Host::where('google_id', $request->google_id)->first();

            if (!$source) {
                return response()->json(['message' => 'Source host not found'], 404);
            }

            $target = Host::find($request->target_host_id);

            if ($source->id === $target->id) {
                return response()->json(['message' => 'Source and target are same']);
            }

            // Save tokens (if requested)
            $tokens = [];
            if ($request->transfer_tokens) {
                $tokens = [
                    'google_access_token' => $source->google_access_token,
                    'google_refresh_token' => $source->google_refresh_token,
                    'google_token_expiry' => $source->google_token_expiry,
                    'google_email' => $source->google_email,
                    'google_name' => $source->google_name,
                ];
            }

            // Unlink source
            $source->update([
                'google_id' => null,
                'google_access_token' => null,
                'google_refresh_token' => null,
                'google_token_expiry' => null,
                'google_calendar_connected' => false,
                'google_email' => null,
                'google_name' => null,
            ]);

            // Link target
            $target->google_id = $request->google_id;

            if ($request->transfer_tokens) {
                $target->google_access_token = $tokens['google_access_token'];
                $target->google_refresh_token = $tokens['google_refresh_token'];
                $target->google_token_expiry = $tokens['google_token_expiry'];
                $target->google_email = $tokens['google_email'];
                $target->google_name = $tokens['google_name'];
                $target->google_calendar_connected = true;
            } else {
                $target->google_calendar_connected = false;
            }

            $target->save();

            return response()->json([
                'message' => 'Google account transferred successfully',
                'source_host' => $source->id,
                'target_host' => $target->id,
                'tokens_transferred' => $request->transfer_tokens
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
