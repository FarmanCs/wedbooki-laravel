<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Host\GuestGroup;
use App\Models\Host\Guest;

class GuestGroupController extends Controller
{
    public function createGroup(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $groupName = strtolower($request->group_name);

        $alreadyExist = GuestGroup::where('group_name', $groupName)
            ->where('host_id', $hostId)
            ->exists();

        if ($alreadyExist) {
            return response()->json(['message' => 'Group name already exists'], 409);
        }

        $group = GuestGroup::create([
            'group_name' => $groupName,
            'host_id' => $hostId,
            'emails' => []
        ]);

        return response()->json($group, 201);
    }

    public function addGuestsToGroup(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'guests' => 'required|array',
            'guests.*.email' => 'required|email',
            'guests.*.full_name' => 'sometimes|string',
            'guests.*.last_name' => 'sometimes|string',
            'group_id' => 'required|exists:guest_groups,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $group = GuestGroup::find($request->group_id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        if ($group->host_id != $hostId) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        $existingEmails = collect($group->guest_info)->pluck('email')->map(function ($email) {
            return strtolower($email);
        })->toArray();

        $duplicateEmails = [];
        $newGuests = [];

        foreach ($request->guests as $guest) {
            $email = strtolower($guest['email']);

            if (in_array($email, $existingEmails)) {
                $duplicateEmails[] = $email;
            } else {
                $newGuests[] = [
                    'email' => $email,
                    'full_name' => $guest['full_name'] ?? '',
                    'last_name' => $guest['last_name'] ?? ''
                ];
            }
        }

        if (!empty($newGuests)) {
            $group->guest_info = array_merge($group->guest_info, $newGuests);
            $group->save();
        }

        return response()->json([
            'message' => 'Guests processed successfully.',
            'added_count' => count($newGuests),
            'skipped_duplicates' => $duplicateEmails,
            'group' => $group
        ]);
    }

    public function getAllGroups($hostId)
    {
        $groups = GuestGroup::where('host_id', $hostId)->get();

        if ($groups->isEmpty()) {
            return response()->json(['message' => 'Groups not found'], 404);
        }

        return response()->json([
            'message' => 'Groups retrieved successfully',
            'groups' => $groups
        ]);
    }

    public function getGroupById($id)
    {
        $group = GuestGroup::with(['host', 'guests'])->find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        return response()->json($group);
    }

    public function updateGroup(Request $request, $id)
    {
        $group = GuestGroup::find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_name' => 'sometimes|string',
            'host_id' => 'sometimes|exists:hosts,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $group->update($request->all());
        $group->load(['host', 'guests']);

        return response()->json($group);
    }

    public function deleteGroup($id)
    {
        $group = GuestGroup::find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        // Delete associated guests
        Guest::whereIn('id', $group->guests->pluck('id'))->delete();

        $group->delete();

        return response()->json([
            'message' => 'Group and its guests deleted successfully'
        ]);
    }

    public function addGuest(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'full_name' => 'sometimes|string',
            'phone_no' => 'sometimes|string',
            'mobile_no' => 'sometimes|string',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string',
            'zipcode' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $group = GuestGroup::find($groupId);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        // Check if guest already exists by email
        $guest = Guest::where('email', strtolower(trim($request->email)))->first();

        if (!$guest) {
            $guest = Guest::create($request->all());
        }

        // Prevent duplicate entry in group
        if ($group->guests->contains($guest->id)) {
            return response()->json(['error' => 'Guest already in group'], 400);
        }

        $group->guests()->attach($guest->id);
        $group->load(['host', 'guests']);

        return response()->json($group);
    }

    public function rsvpGuest($guestId, Request $request)
    {
        $status = $request->query('status');

        if (!in_array($status, ['Accepted', 'Rejected', 'Pending'])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $guest = Guest::find($guestId);
        if (!$guest) {
            return response()->json(['error' => 'Guest not found'], 404);
        }

        $guest->update(['is_joining' => $status]);

        return response()->view('rsvp-response', [
            'guestName' => $guest->full_name ?? 'Guest',
            'status' => $status
        ]);
    }
}
