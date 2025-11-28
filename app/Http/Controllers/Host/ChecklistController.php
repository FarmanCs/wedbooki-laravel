<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host\ChecklistTemplate;
use App\Models\Host\Host;
use App\Models\Host\HostPersonalizedChecklist;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ChecklistController extends Controller
{
    public function createTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eventType' => 'required|string',
            'checklistItems' => 'required|array',
            'checklistItems.*.CheckListTitle' => 'required|string',
            'checklistItems.*.CheckListCategory' => 'required|string',
            'checklistItems.*.CheckListDescription' => 'required|string',
            'checklistItems.*.CheckListDueDate' => 'nullable|date',
            'checklistItems.*.ChecklistStatus' => 'required|string',
            'checklistItems.*.isCustom' => 'required|boolean',
            'checklistItems.*.isEdited' => 'required|boolean',
            'checklistItems.*.lockToWeddingDate' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()->first(),
            ], 400);
        }
//check for exesting templets
        $existingTemplate = ChecklistTemplate::where('event_type', $request->eventType)->first();

        if ($existingTemplate) {
            return response()->json([
                'message' => "Checklist template for event type '{$request->eventType}' already exists."
            ], 400);
        }

        $template = ChecklistTemplate::create([
            'event_type' => $request->eventType,
            'checklist_items' => json_encode($request->checklistItems)
        ]);

        return response()->json([
            'message' => "{$request->event_type} checklist template created successfully",
            'template' => $template
        ], 201);
    }
    public function assignChecklist(Request $request)
    {
        // Get host id from authenticated user (Sanctum)
        $hostId = auth()->id();

        if (!$hostId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string',
            'wedding_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Fetch host
        $host = Host::find($hostId);
        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        $today = Carbon::today();
        $eventDate = Carbon::parse($request->wedding_date);

        if ($today->gte($eventDate)) {
            return response()->json(['message' => 'Wedding date must be after today.'], 400);
        }

        // Fetch checklist template for the event type
        $template = ChecklistTemplate::where('event_type', $request->event_type)->first();
        if (!$template) {
            return response()->json([
                'message' => "No template found for event type: {$request->event_type}"
            ], 404);
        }

        $checklistItems = $template->checklist_items ?? [];
        $totalDays = $today->diffInDays($eventDate);
        if ($totalDays <= 0) {
            return response()->json(['message' => 'Invalid wedding date range.'], 400);
        }

        $itemsPerDay = ceil(count($checklistItems) / $totalDays);

        $personalizedChecklist = $host->personalized_checklist ?? [];
        $currentDay = 1;
        $itemCount = 0;

        foreach ($checklistItems as $item) {
            $shouldLock = $item['lock_to_wedding_date'] ?? false;

            // Find existing non-custom item
            $existingItemKey = collect($personalizedChecklist)->search(function ($c) use ($item) {
                return ($c['check_list_title'] ?? null) === ($item['check_list_title'] ?? null)
                    && ($c['check_list_category'] ?? null) === ($item['check_list_category'] ?? null)
                    && !($c['is_custom'] ?? false);
            });

            if ($existingItemKey !== false) {
                $existingItem = $personalizedChecklist[$existingItemKey];

                if (!($existingItem['is_edited'] ?? false)) {
                    if ($shouldLock) {
                        $existingItem['check_list_due_date'] = $eventDate->toDateString();
                        $existingItem['lock_to_wedding_date'] = true;
                    } else {
                        $existingItem['check_list_due_date'] = $today->copy()->addDays($currentDay)->toDateString();
                        $existingItem['lock_to_wedding_date'] = false;
                    }
                    $personalizedChecklist[$existingItemKey] = $existingItem;
                }
            } else {
                // Add new checklist item
                $dueDate = $shouldLock ? $eventDate->toDateString() : $today->copy()->addDays($currentDay)->toDateString();

                $personalizedChecklist[] = [
                    'check_list_title' => $item['check_list_title'] ?? null,
                    'check_list_category' => $item['check_list_category'] ?? null,
                    'check_list_description' => $item['check_list_description'] ?? null,
                    'check_list_due_date' => $dueDate,
                    'checklist_status' => 'pending',
                    'is_custom' => false,
                    'is_edited' => false,
                    'lock_to_wedding_date' => $shouldLock,
                ];
            }

            // Distribute non-locked items across days
            if (!$shouldLock) {
                $itemCount++;
                if ($itemCount >= $itemsPerDay) {
                    $currentDay++;
                    $itemCount = 0;
                }
            }
        }

        // Save updated host data
        $host->update([
            'event_type' => $request->event_type,
            'wedding_date' => $eventDate->toDateString(),
            'personalized_checklist' => $personalizedChecklist,
        ]);

        return response()->json([
            'message' => 'Checklist assigned successfully.',
            'host_id' => $host->id,
            'event_type' => $request->event_type,
            'total_days' => $totalDays,
            'checklist' => $personalizedChecklist,
        ]);
    }
    public function toggleChecklistStatus(Request $request)
    {
        try {
            $host = auth()->user();

            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            $validator = Validator::make($request->all(), [
                'itemId' => 'required|integer|exists:personalized_checklists,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Checklist item ID is required.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $itemId = $request->itemId;

            // Get the checklist item for the host
            $item = HostPersonalizedChecklist::where('host_id', $host->id)
                ->where('id', $itemId)
                ->first();

            if (!$item) {
                return response()->json(['message' => 'Checklist item not found.'], 404);
            }

            // Toggle status
            $item->checklist_status = $item->checklist_status === 'pending' ? 'checked' : 'pending';
            $item->save();

            return response()->json([
                'message' => 'Checklist status updated successfully.',
                'itemId' => $itemId,
                'newStatus' => $item->checklist_status
            ], 200);

        } catch (\Exception $error) {
            \Log::error("ToggleChecklistStatus Error: ".$error->getMessage());

            return response()->json(['message' => 'Server error.'], 500);
        }
    }
    public function addCustomChecklistItem(Request $request)
    {
        try {
            $host = auth()->user();
            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
            $validator = Validator::make($request->all(), [
                'check_list_title'               => 'required|string',
                'check_list_category'            => 'required|string',
                'check_list_description'         => 'nullable|string',
                'check_list_due_date'            => 'required|date',
                'check_list_item_linked_with_id' => 'nullable|integer|exists:businesses,id',
                'checklist_linked_booking_id'    => 'nullable|integer|exists:bookings,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()
                ], 400);
            }

            $data = $validator->validated();

            //  Get linked business name
            $linkedBusinessName = null;
            if (!empty($data['check_list_item_linked_with_id'])) {
                $business = Business::where('id', $data['check_list_item_linked_with_id'])
                    ->select('company_name')
                    ->first();

                if (!$business) {
                    return response()->json([
                        'message' => 'Invalid linked business ID.'
                    ], 400);
                }

                $linkedBusinessName = $business->company_name;
            }

            // Get booking custom_booking_id
            $linkedBookingCustomId = null;
            if (!empty($data['checklist_linked_booking_id'])) {
                $booking = Booking::where('id', $data['checklist_linked_booking_id'])
                    ->select('custom_booking_id')
                    ->first();

                $linkedBookingCustomId = $booking ? $booking->custom_booking_id : null;
            }

            //  Build data for DB insert
            $newChecklistData = [
                'host_id'                        => $host->id,
                'check_list_title'               => $data['check_list_title'] ,
                'check_list_category'            => $data['check_list_category'] ,
                'check_list_description'         => $data['check_list_description'] ,
                'check_list_due_date'            => $data['check_list_due_date'],
                'checklist_status'               => 'pending',
                'is_custom'                      => true,
                'is_edited'                      => false,
                'lock_to_wedding_date'           => $request->lock_to_wedding_date ?? null,

                'check_list_item_linked_with'    => $linkedBusinessName,
                'check_list_item_linked_with_id' => $data['check_list_item_linked_with_id'] ?? null,

                'checklist_linked_booking'       => $linkedBookingCustomId,
                'checklist_linked_booking_id'    => $data['checklist_linked_booking_id'] ?? null,
            ];

            //  Insert into DB
            $createdItem = HostPersonalizedChecklist::create($newChecklistData);
            return response()->json([
                'message'   => 'Custom checklist item added successfully.',
                'hostId'    => $host->id,
                'checklist' => $createdItem
            ], 201);

        } catch (\Exception $e) {
            \Log::error("AddCustomChecklistItem Error: ".$e->getMessage());
            return response()->json([
                'message' => 'Server error.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function deleteChecklistItem(Request $request)
    {
        try {
            // ---------------------------------------
            // 1. AUTH: Get host from Sanctum
            // ---------------------------------------
            $host = auth()->user();

            if (!$host) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 401);
            }

            // ---------------------------------------
            // 2. Validate input
            // ---------------------------------------
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|integer|exists:personalized_checklists,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()
                ], 400);
            }

            $itemId = $request->item_id;

            // ---------------------------------------
            // 3. Find the checklist item belonging to this host
            // ---------------------------------------
            $checklistItem = HostPersonalizedChecklist::where('host_id', $host->id)
                ->where('id', $itemId)
                ->first();

            if (!$checklistItem) {
                return response()->json([
                    'message' => 'Checklist item not found.'
                ], 404);
            }

            // ---------------------------------------
            // 4. Delete the item
            // ---------------------------------------
            $checklistItem->delete();

            // ---------------------------------------
            // 5. Fetch remaining checklist for response
            // ---------------------------------------
            $remainingChecklist = HostPersonalizedChecklist::where('host_id', $host->id)->get();

            return response()->json([
                'message'             => 'Checklist item deleted successfully.',
                'hostId'              => $host->id,
                'remainingChecklist'  => $remainingChecklist
            ], 200);

        } catch (\Exception $e) {
            \Log::error("DeleteChecklistItem Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server error.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function editChecklistItem(Request $request, $itemId)
    {
        try {
            // 1️⃣ Get authenticated host via Sanctum
            $host = auth()->user();
            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            if (!$itemId) {
                return response()->json([
                    'message' => 'Checklist Item ID is required.'
                ], 400);
            }

            // 2️⃣ Validate optional fields
            $validator = Validator::make($request->all(), [
                'check_list_title'                => 'nullable|string',
                'check_list_category'             => 'nullable|string',
                'check_list_description'          => 'nullable|string',
                'check_list_due_date'             => 'nullable|date',
                'check_list_item_linked_with_id'  => 'nullable|integer|exists:businesses,id',
                'checklist_linked_booking_id'     => 'nullable|integer|exists:bookings,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()
                ], 400);
            }

            // 3️⃣ Find the checklist item for this host
            $checklistItem = HostPersonalizedChecklist::where('host_id', $host->id)
                ->where('id', $itemId)
                ->first();

            if (!$checklistItem) {
                return response()->json([
                    'message' => 'Checklist item not found.'
                ], 404);
            }

            $data = $request->all();

            // 4️⃣ Update fields if provided
            if (isset($data['check_list_title'])) {
                $checklistItem->check_list_title = $data['check_list_title'];
            }
            if (isset($data['check_list_category'])) {
                $checklistItem->check_list_category = $data['check_list_category'];
            }
            if (isset($data['check_list_description'])) {
                $checklistItem->check_list_description = $data['check_list_description'];
            }
            if (isset($data['check_list_due_date'])) {
                $checklistItem->check_list_due_date = $data['check_list_due_date'];
            }

            // 5️⃣ Update linked booking if provided
            if (isset($data['checklist_linked_booking_id'])) {
                $linkedBooking = Booking::select('custom_booking_id')
                    ->where('id', $data['checklist_linked_booking_id'])
                    ->first();
                $checklistItem->checklist_linked_booking = $linkedBooking ? $linkedBooking->custom_booking_id : null;
                $checklistItem->checklist_linked_booking_id = $data['checklist_linked_booking_id'];
            }

            // 6️⃣ Update linked business if provided
            if (isset($data['check_list_item_linked_with_id'])) {
                $linkedBusiness = Business::select('company_name')
                    ->where('id', $data['check_list_item_linked_with_id'])
                    ->first();

                if (!$linkedBusiness) {
                    return response()->json([
                        'message' => 'Invalid linked business ID provided.'
                    ], 400);
                }

                $checklistItem->check_list_item_linked_with = $linkedBusiness->company_name;
                $checklistItem->check_list_item_linked_with_id = $data['check_list_item_linked_with_id'];
            }

            // 7️⃣ Mark as edited
            $checklistItem->is_edited = true;

            // 8️⃣ Save changes
            $checklistItem->save();

            return response()->json([
                'message' => 'Checklist item updated successfully.',
                'updatedItem' => $checklistItem
            ], 200);

        } catch (\Exception $e) {
            \Log::error("EditChecklistItem Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server Error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllTemplates()
    {
        $templates = ChecklistTemplate::all();
        $resp = collect([]);

        $templates->map(function ($template) use (&$resp) {
            $item['eventType'] = $template->event_type;
            $item['checklistItems'] = $template->checklist_items;
            $resp->push($item);
        });

        return response()->json([
            'message' => 'Templates retrieved successfully.',
            'templates' => $resp,
        ]);
    }
}
