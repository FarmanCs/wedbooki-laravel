<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Review;
use App\Models\Vendor\Booking;
use App\Models\VendorReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function giveReview(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'review_text' => 'required|string',
            'points' => 'required|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = \App\Models\Host::find($hostId);
        if (!$host) {
            return response()->json(['message' => 'Host does not exist'], 404);
        }

        $business = Business::find($request->business_id);
        if (!$business) {
            return response()->json(['message' => 'Vendor does not exist'], 404);
        }

        // Check if host has a confirmed booking with this vendor
        $hasBooking = Booking::where('business_id', $request->business_id)
            ->where('host_id', $hostId)
            ->where('status', 'accepted')
            ->exists();

        if (!$hasBooking) {
            return response()->json([
                'message' => 'You cannot review this vendor without a confirmed booking.'
            ], 403);
        }

        // Check if already reviewed
        $alreadyReviewed = Review::where('host_id', $hostId)
            ->where('business_id', $request->business_id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'message' => 'You have already reviewed this vendor.'
            ], 400);
        }

        $review = Review::create([
            'host_id' => $hostId,
            'business_id' => $request->business_id,
            'text' => $request->review_text,
            'points' => $request->points
        ]);

        $business->reviews()->attach($review->id);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ], 201);
    }

    public function editReview(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
            'review_text' => 'sometimes|string',
            'points' => 'sometimes|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $review = Review::find($request->review_id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        if ($review->host_id != $hostId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $updateData = [];
        if ($request->has('review_text')) {
            $updateData['text'] = $request->review_text;
        }
        if ($request->has('points')) {
            $updateData['points'] = $request->points;
        }

        $review->update($updateData);

        return response()->json([
            'message' => 'Review updated',
            'review' => $review
        ]);
    }

    public function deleteReview(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = \App\Models\Host::find($hostId);
        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        $review = Review::where('id', $request->review_id)
            ->where('host_id', $hostId)
            ->first();

        if (!$review) {
            return response()->json([
                'message' => 'Review not found or unauthorized'
            ], 404);
        }

        // Delete associated vendor replies
        VendorReply::whereIn('id', $review->vendor_replies ?? [])->delete();

        // Remove review from vendor
        $review->business->reviews()->detach($review->id);

        // Delete the review
        $review->delete();

        return response()->json([
            'message' => 'Review and associated replies deleted successfully'
        ]);
    }

    public function getAllVendorReviews($vendorId)
    {
        $vendor = \App\Models\Vendor::with(['business'])
            ->where('id', $vendorId)
            ->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $reviews = Review::with(['host', 'vendorReplies.vendor'])
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No reviews found'], 404);
        }

        $averageRating = $reviews->avg('points');
        $totalReviews = $reviews->count();

        return response()->json([
            'message' => 'Reviews found',
            'vendor' => $vendor,
            'average_rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews,
            'reviews' => $reviews
        ]);
    }
}
