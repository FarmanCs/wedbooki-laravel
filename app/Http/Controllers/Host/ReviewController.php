<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host\Review;
use App\Models\Host\Host;
use App\Models\Vendor;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\VendorReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function giveReview(Request $request)
    {
        try {
            $hostId = auth()->id(); // Sanctum Auth ID

            $validator = Validator::make($request->all(), [
                'business_id' => 'required|exists:businesses,id',
                'review_text' => 'required|string',
                'points' => 'required|integer|min:1|max:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $businessId = $request->business_id;

            // Check host
            $host = Host::find($hostId);
            if (!$host) {
                return response()->json([
                    'message' => 'Host does not exist'
                ], 404);
            }

            // Check business/vendor
            $business = Business::find($businessId);
            if (!$business) {
                return response()->json([
                    'message' => 'Vendor does not exist'
                ], 404);
            }

            // Check if user has a confirmed booking
            $hasBooking = Booking::where('business_id', $businessId)
                ->where('host_id', $hostId)
                ->where('status', 'accepted')
                ->exists();

//            if (!$hasBooking) {
//                return response()->json([
//                    'message' => 'You cannot review this vendor without a confirmed booking.'
//                ], 403);
//            }

            // Check if already reviewed
            $alreadyReviewed = Review::where('host_id', $hostId)
                ->where('business_id', $businessId)
                ->exists();

            if ($alreadyReviewed) {
                return response()->json([
                    'message' => 'You have already reviewed this vendor.'
                ], 400);
            }

            // Rating range check (Same as Express)
            if ($request->points < 1 || $request->points > 5) {
                return response()->json([
                    'message' => 'Rating must be between 1 and 5.'
                ], 400);
            }

            // Create review
            $review = Review::create([
                'host_id'     => $hostId,
                'business_id' => $businessId,
                'text'        => $request->review_text,
                'points'      => $request->points,
            ]);

            // In Express they push review ID into business.reviews[]
            // In Laravel, this equals using a many-to-many or hasMany.
            // If your DB has business_id in reviews table, NO need to attach.
            // Only attach if you have pivot table business_review.
            if (method_exists($business, 'reviews')) {
                $business->reviews()->save($review);
            }

            return response()->json([
                'message' => 'Review submitted successfully',
                'review'  => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function editReview(Request $request)
    {
       try{
           $hostId = auth()->id();
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
       catch (\Exception $e) {
           return response()->json([
               'message' => 'Please try again later.',
               'error'   => $e->getMessage()
           ], 500);
       }
    }

    public function deleteReview(Request $request)
    {
        try {
            $hostId = auth()->id();

            // Validate review ID
            $validator = Validator::make($request->all(), [
                'review_id' => 'required|exists:reviews,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // 1. Ensure the authenticated host exists
            $host = Host::find($hostId);
            if (!$host) {
                return response()->json(['message' => 'Host not found.'], 404);
            }

            // 2. Find the review that belongs to this host
            $review = Review::where('id', $request->review_id)
                ->where('host_id', $hostId)
                ->first();

            if (!$review) {
                return response()->json([
                    'message' => 'Review not found or unauthorized.'
                ], 404);
            }

            // 3. Delete associated vendor replies
            VendorReply::where('review_id', $review->id)->delete();

            // 4. Remove review from vendor/business relationship
            // (Equivalent to $pull in MongoDB)
            if ($review->business) {
                $review->business->reviews()
                    ->where('id', $review->id)
                    ->delete(); // deletes business->reviews relation if using pivot or hasMany
            }

            // 5. Delete the review itself
            $review->delete();

            return response()->json([
                'message' => 'Review and associated replies deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAllVendorReviews(Request $request)
    {
        try {
            // 1. Get the authenticated vendor's ID via Sanctum
            $hostId = auth()->id();

           


            // 3. Fetch reviews with host info and vendor replies + vendor info
            $reviews = Review::where('host_id', $hostId)
                ->with(['business'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($reviews->isEmpty()) {
                return response()->json(['message' => 'No reviews found'], 404);
            }

            // 4. Calculate average rating
            $totalRating = $reviews->sum('points');
            $averageRating = $reviews->count() > 0
                ? number_format($totalRating / $reviews->count(), 2)
                : 0;

            // 5. Respond with same structure as Express.js
            return response()->json([
                'message'       => 'Reviews found',
                'vendor'        => $vendor,
                'averageRating' => (float) $averageRating,
                'totalReviews'  => $reviews->count(),
                'reviews'       => $reviews
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
