<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host\ChecklistTemplate;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function assignChecklist(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string',
            'wedding_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = Host::find($hostId);
        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        $today = now();
        $eventDate = \Carbon\Carbon::parse($request->wedding_date);

        if ($today->gte($eventDate)) {
            return response()->json([
                'message' => 'Wedding date must be after today.'
            ], 400);
        }

        $template = ChecklistTemplate::where('event_type', $request->event_type)->first();
        if (!$template) {
            return response()->json([
                'message' => "No template found for event type: {$request->event_type}"
            ], 404);
        }

        $totalDays = $today->diffInDays($eventDate);
        $itemsPerDay = ceil(count($template->checklist_items) / $totalDays);

        $personalizedChecklist = $host->personalized_checklist ?? [];
        $currentDay = 1;
        $itemCount = 0;

        foreach ($template->checklist_items as $item) {
            $shouldLock = $item['lock_to_wedding_date'] ?? false;

            $existingItem = collect($personalizedChecklist)->first(function ($c) use ($item) {
                return $c['check_list_title'] === $item['check_list_title'] &&
                    $c['check_list_category'] === $item['check_list_category'] &&
                    !($c['is_custom'] ?? false);
            });

            if ($existingItem) {
                if (!($existingItem['is_edited'] ?? false)) {
                    if ($shouldLock) {
                        $existingItem['check_list_due_date'] = $eventDate;
                        $existingItem['lock_to_wedding_date'] = true;
                    } else {
                        $dueDate = $today->copy()->addDays($currentDay);
                        $existingItem['check_list_due_date'] = $dueDate;
                        $existingItem['lock_to_wedding_date'] = false;
                    }
                }
            } else {
                $dueDate = $shouldLock ? $eventDate : $today->copy()->addDays($currentDay);

                $personalizedChecklist[] = [
                    'check_list_title' => $item['check_list_title'],
                    'check_list_category' => $item['check_list_category'],
                    'check_list_description' => $item['check_list_description'],
                    'check_list_due_date' => $dueDate,
                    'checklist_status' => 'pending',
                    'is_custom' => false,
                    'is_edited' => false,
                    'lock_to_wedding_date' => $shouldLock
                ];
            }

            if (!$shouldLock) {
                $itemCount++;
                if ($itemCount >= $itemsPerDay) {
                    $currentDay++;
                    $itemCount = 0;
                }
            }
        }

        $host->update([
            'event_type' => $request->event_type,
            'wedding_date' => $eventDate,
            'personalized_checklist' => $personalizedChecklist
        ]);

        return response()->json([
            'message' => 'Checklist assigned successfully.',
            'host_id' => $host->id,
            'event_type' => $request->event_type,
            'total_days' => $totalDays,
            'checklist' => $personalizedChecklist
        ]);
    }

    public function toggleChecklistStatus(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = Host::find($hostId);
        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        $checklistItem = collect($host->personalized_checklist)
            ->firstWhere('_id', $request->item_id);

        if (!$checklistItem) {
            return response()->json(['message' => 'Checklist item not found'], 404);
        }

        // Update the status in the personalized_checklist array
        $updatedChecklist = collect($host->personalized_checklist)
            ->map(function ($item) use ($request) {
                if ($item['_id'] == $request->item_id) {
                    $item['checklist_status'] =
                        $item['checklist_status'] === 'pending' ? 'checked' : 'pending';
                }
                return $item;
            })
            ->toArray();

        $host->update(['personalized_checklist' => $updatedChecklist]);

        return response()->json([
            'message' => 'Checklist status updated successfully.',
            'item_id' => $request->item_id,
            'new_status' => $checklistItem['checklist_status'] === 'pending' ? 'checked' : 'pending'
        ]);
    }

    public function addCustomChecklistItem(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'check_list_due_date' => 'required|date',
            'check_list_title' => 'required|string',
            'check_list_category' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = Host::find($hostId);
        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        $linkedBusinessName = "";
        if ($request->has('check_list_item_linked_with')) {
            $linkedBusiness = Business::find($request->check_list_item_linked_with);
            if (!$linkedBusiness) {
                return response()->json(['message' => 'Invalid linked business ID'], 400);
            }
            $linkedBusinessName = $linkedBusiness->company_name;
        }

        $linkedBookingId = null;
        if ($request->has('checklist_linked_booking')) {
            $linkedBooking = Booking::find($request->checklist_linked_booking);
            $linkedBookingId = $linkedBooking ? $linkedBooking->custom_booking_id : null;
        }

        $newItem = array_merge($request->all(), [
            'check_list_item_linked_with' => $linkedBusinessName,
            'checklist_linked_booking' => $linkedBookingId,
            'check_list_item_linked_with_id' => $request->check_list_item_linked_with ?? null,
            'checklist_linked_booking_id' => $request->checklist_linked_booking ?? null,
            'checklist_status' => 'pending',
            'is_custom' => true,
            'is_edited' => false
        ]);

        $personalizedChecklist = $host->personalized_checklist ?? [];
        $personalizedChecklist[] = $newItem;

        $host->update(['personalized_checklist' => $personalizedChecklist]);

        return response()->json([
            'message' => 'Custom checklist item added successfully.',
            'host_id' => $host->id,
            'checklist' => $personalizedChecklist
        ], 201);
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
