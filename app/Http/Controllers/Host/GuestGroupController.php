<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Host\GuestGroup;
use App\Models\Host\Guest;
use Illuminate\Support\Facades\DB;

class GuestGroupController extends Controller
{
    // --------------------------------------------------
    // CREATE GROUP  (Express: createGroup)
    // --------------------------------------------------
    public function createGuestGroup(Request $request)
    {
        // 1. Validate request
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Get the authenticated host (Sanctum)
        $hostId = auth()->id();

        // 3. Normalize group name like Express version
        $normalizedName = strtolower(trim($request->group_name));

        // 4. Check if group already exists for this host
        $alreadyExists = GuestGroup::where('host_id', $hostId)
            ->where('group_name', $normalizedName)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Group name already exists'
            ], 409);
        }

        // 5. Create the group
        $group = GuestGroup::create([
            'group_name' => $normalizedName,
            'host_id' => $hostId,
        ]);

        return response()->json([
            'group' => $group
        ], 201);
    }

    public function addGuestsToGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guests' => 'required|array',
            'guests.*.email' => 'required|email',
            'guests.*.full_name' => 'required|string',
            'guests.*.last_name' => 'required|string',
            'group_id' => 'required|exists:guest_groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $hostId = auth()->id();
        $group = GuestGroup::find($request->group_id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // ensure group belongs to authenticated host
        if ((int)$group->host_id !== (int)$hostId) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        // existing emails already attached to the group (lowercased)
        $existingEmails = $group->guests()->pluck('email')->map(function ($e) {
            return strtolower($e);
        })->toArray();

        $incoming = $request->guests;
        $duplicateEmails = [];
        $addedCount = 0;
        $attachIds = [];

        DB::beginTransaction();
        try {
            foreach ($incoming as $g) {
                // skip items without email (mirrors Express behavior)
                if (empty($g['email'])) {
                    continue;
                }

                $email = strtolower(trim($g['email']));

                if (in_array($email, $existingEmails)) {
                    $duplicateEmails[] = $email;
                    continue;
                }

                // build full_name: prefer full_name, if missing and last_name present combine
                $fullName = isset($g['full_name']) && strlen(trim($g['full_name'])) > 0
                    ? trim($g['full_name'])
                    : '';

                if (empty($fullName) && !empty($g['last_name'])) {
                    $fullName = trim($g['last_name']);
                }

                // Find existing guest by email, create if not found
                $guest = Guest::where('email', $email)->first();
                if (!$guest) {
                    $guest = Guest::create([
                        'email' => $email,
                        'full_name' => $fullName,
                        'phone_no' => $g['phone_no'] ?? null,
                        'mobile_no' => $g['mobile_no'] ?? null,
                        'address' => $g['address'] ?? null,
                        'city' => $g['city'] ?? null,
                        'state' => $g['state'] ?? null,
                        'zipcode' => $g['zipcode'] ?? null,
                    ]);
                }

                // Prevent duplicate pivot entries just in case
                if (!$group->guests()->where('guest_id', $guest->id)->exists()) {
                    $attachIds[] = $guest->id;
                    // also mark email as present to avoid duplicate incoming entries in same request
                    $existingEmails[] = $email;
                    $addedCount++;
                } else {
                    $duplicateEmails[] = $email;
                }
            }

            // Attach all new guest ids at once, without removing existing (like push)
            if (!empty($attachIds)) {
                $group->guests()->syncWithoutDetaching($attachIds);
            }

            DB::commit();

            // reload with relations to return
            $group->load(['host', 'guests']);

            return response()->json([
                'message' => 'Guests processed successfully.',
                'addedCount' => $addedCount,
                'skippedDuplicates' => array_values(array_unique($duplicateEmails)),
                'group' => $group
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error adding guests to group: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getMyGroups()
    {
        try {
            // 1. Get authenticated host ID via Sanctum
            $hostId = auth()->id();

            // 2. Fetch all groups for this host
            $groups = GuestGroup::where('host_id', $hostId)
                ->with('guests') // optional: populate guests if needed
                ->get();

            // 3. Handle empty result
            if ($groups->isEmpty()) {
                return response()->json([
                    'message' => 'No groups found'
                ], 404);
            }

            // 4. Return groups
            return response()->json([
                'message' => 'Groups retrieved successfully',
                'groups' => $groups
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Error fetching host groups: ' . $e->getMessage());
            return response()->json([
                'message' => 'Please try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // --------------------------------------------------
    // GET ALL GROUPS (with guests)
    // --------------------------------------------------
    public function getAllGroups()
    {
        try {
            $groups = GuestGroup::all();

            return response()->json([
                "message" => "All guest groups fetched",
                "groups" => $groups
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // --------------------------------------------------
    // GET GROUP BY ID (with host & guests)
    // --------------------------------------------------
    public function getGroupById($id)
    {
        $group = GuestGroup::with(['host', 'guests'])->find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        return response()->json($group);
    }

    // --------------------------------------------------
    // UPDATE GROUP
    // --------------------------------------------------
    public function updateGroup(Request $request, $id)
    {
        $group = GuestGroup::find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        // Normalize group_name if present
        if ($request->has('group_name')) {
            $request['group_name'] = strtolower(trim($request->group_name));
        }

        $group->update($request->only(['group_name', 'host_id']));

        $group->load(['host', 'guests']);

        return response()->json($group);
    }

    // --------------------------------------------------
    // DELETE GROUP + its guests
    // --------------------------------------------------
    public function deleteGroup($id)
    {
        $group = GuestGroup::find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        // Delete all guests linked with this group
        $guestIds = $group->guests()->pluck('guests.id');

        Guest::whereIn('id', $guestIds)->delete();

        // Delete pivot rows
        $group->guests()->detach();

        // Delete group
        $group->delete();

        return response()->json(['message' => 'Group and its guests deleted successfully']);
    }

    // --------------------------------------------------
    // ADD GUEST TO GROUP
    // --------------------------------------------------
    public function addGuest(Request $request, $groupId)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'full_name' => 'nullable|string',
                'phone_no' => 'nullable|string',
                'mobile_no' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'zipcode' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Find group
            $group = GuestGroup::find($groupId);
            if (!$group) {
                return response()->json(['error' => 'Group not found'], 404);
            }

            // Normalize email
            $email = strtolower(trim($request->email));

            // Check if guest exists
            $guest = Guest::where('email', $email)->first();

            // If not found, create new one
            if (!$guest) {
                $guest = Guest::create([
                    'email' => $email,
                    'full_name' => $request->full_name,
                    'phone_no' => $request->phone_no,
                    'mobile_no' => $request->mobile_no,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zipcode' => $request->zipcode
                ]);
            }

            // Prevent duplicate entry in pivot table
            $alreadyExists = $group->guests()
                ->where('guest_id', $guest->id)
                ->exists();

            if ($alreadyExists) {
                return response()->json(['error' => 'Guest already in group'], 400);
            }

            // Attach guest to group
            $group->guests()->attach($guest->id);

            // Load relationships for final response
//            $group->load(['host', 'guests']);

            return response()->json($group, 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // --------------------------------------------------
    // UPDATE GUEST
    // --------------------------------------------------
    public function updateGuest(Request $request, $guestId)
    {
        try {
            // Get authenticated host ID
            $hostId = auth()->user()->id;

            if (!$hostId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Find the guest AND ensure it belongs to a group of this host
            $guest = Guest::where('id', $guestId)
                ->whereHas('groups', function ($query) use ($hostId) {
                    $query->where('host_id', $hostId);
                })
                ->first();

            if (!$guest) {
                return response()->json(['error' => 'Guest not found or does not belong to this host'], 404);
            }

            // Update email
            if ($request->filled('email')) {
                $guest->email = strtolower(trim($request->email));
            }

            // Other fields
            $fields = ['full_name', 'phone_no', 'mobile_no', 'address', 'city', 'state', 'zipcode'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $guest->$field = $request->$field;
                }
            }

            // Save changes
            $guest->save();

            return response()->json([
                'message' => 'Guest updated successfully',
                'guest' => $guest
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------
    // RSVP GUEST
    // --------------------------------------------------
    public function rsvpGuest(Request $request, $guestId)
    {
        try {
            // Validate status from query
            $status = $request->query('status');

            if (!in_array($status, ['Accepted', 'Rejected', 'Pending'])) {
                return response()->json(['error' => 'Invalid status'], 400);
            }

            // Find guest
            $guest = Guest::find($guestId);

            if (!$guest) {
                return response()->json(['error' => 'Guest not found'], 404);
            }

            // Update RSVP status
            $guest->is_joining = $status;
            $guest->save();

            // Return HTML like Express.js
            $html = "
            <html>
                <body style='font-family: Arial, sans-serif; text-align:center; margin-top:50px;'>
                    <h2>Thanks, " . ($guest->full_name ?? "Guest") . "!</h2>
                    <p style='font-size:18px;'>
                        You have
                        <strong style='color:" . ($status === "Accepted" ? "#28a745" : "#dc3545") . ";'>
                            $status
                        </strong>
                        the invitation.
                    </p>
                </body>
            </html>
        ";

            return response($html, 200)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // REMOVE GUEST FROM GROUP
    public function deleteGuest(Request $request, $group_id)
    {
        $group = GuestGroup::find($group_id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        if (!$request->guest_id) {
            return response()->json(['error' => 'guest_id is required'], 400);
        }

        // Check membership
        if (!$group->guests()->where('guest_id', $request->guest_id)->exists()) {
            return response()->json(['error' => 'Guest not in group'], 404);
        }

        // Remove from pivot
        $group->guests()->detach($request->guest_id);

        // Delete guest
        Guest::find($request->guest_id)?->delete();

//        $group->load(['host', 'guests']);

        return response()->json(["message"=>"guest deleted succesfuly"]);
    }
}
