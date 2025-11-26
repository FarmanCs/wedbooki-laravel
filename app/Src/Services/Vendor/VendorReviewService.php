<?php

namespace App\Src\Services\Vendor;

use App\Models\Host\Review;
use App\Models\Vendor;
use App\Models\Vendor\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

//use App\Models\ReviewReply;

class VendorReviewService
{
    public function getAllMyReviews($businessId): JsonResponse
    {
        $reviews = Review::where('business_id', $businessId)
            ->with(['host:id,full_name,profile_image', 'vendorReplies.vendor:id,full_name'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No reviews found'], 404);
        }

        return response()->json([
            'message' => 'Reviews found',
            'reviews' => $reviews
        ], 200);
    }

    public function replyToReview($businessId, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'reviewId' => 'required|exists:reviews,id',
            'responseText' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $business = Business::find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        $vendor = Vendor::where('business_profile_id', $businessId)->first();
        $review = Review::find($data['reviewId']);

        if (!$review) {
            return response()->json(['message' => 'Review not found.'], 404);
        }

        if (count($review->vendor_replies ?? []) >= 2) {
            return response()->json(['message' => 'Maximum 2 replies allowed per review.'], 400);
        }

        if ($review->business_id != $businessId) {
            return response()->json(['message' => 'You can only reply to your own reviews.'], 403);
        }

        $reply = ReviewReply::create([
            'review_id' => $review->id,
            'business_id' => $business->id,
            'vendor_id' => $vendor->id,
            'text' => $data['responseText'],
        ]);

        $replies = $review->vendor_replies ?? [];
        $replies[] = $reply->id;
        $review->vendor_replies = $replies;
        $review->save();

        return response()->json([
            'message' => 'Reply submitted successfully.',
            'review' => $review->fresh()
        ], 200);
    }

    public function updateReply($vendorId, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'replyId' => 'required|exists:review_replies,id',
            'replyText' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $reply = ReviewReply::find($data['replyId']);

        if (!$reply) {
            return response()->json(['message' => 'Reply not found.'], 404);
        }

        $review = Review::find($reply->review_id);

        if (!$review) {
            return response()->json(['message' => 'Review not found.'], 404);
        }

        if ($review->vendor_id != $vendorId) {
            return response()->json(['message' => 'Not authorized to update this reply.'], 403);
        }

        $reply->text = $data['replyText'];
        $reply->save();

        return response()->json([
            'message' => 'Reply updated successfully.',
            'reply' => $reply
        ], 200);
    }

    public function deleteReply($vendorId, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'replyId' => 'required|exists:review_replies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $reply = ReviewReply::find($data['replyId']);

        if (!$reply) {
            return response()->json(['message' => 'Reply not found.'], 404);
        }

        $review = Review::find($reply->review_id);

        if (!$review) {
            return response()->json(['message' => 'Review not found.'], 404);
        }

        if ($review->vendor_id != $vendorId) {
            return response()->json(['message' => 'Not authorized to delete this reply.'], 403);
        }

        $reply->delete();

        // Remove from review replies array
        $replies = $review->vendor_replies ?? [];
        $replies = array_filter($replies, fn($id) => $id !== $data['replyId']);
        $review->vendor_replies = array_values($replies);
        $review->save();

        return response()->json(['message' => 'Reply deleted successfully.'], 200);
    }
}
