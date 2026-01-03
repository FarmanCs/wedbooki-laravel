<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host\Favourites;
use App\Models\Host\Review;
use App\Models\Vendor\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    public function addFavourite(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Auth host from Sanctum
        $hostId = auth()->id();
        $businessId = $request->business_id;

        // Check if favourite already exists
        $existing = Favourites::where('host_id', $hostId)
            ->where('business_id', $businessId)
            ->first();

        if ($existing) {
            // Remove favourite
            $existing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Removed from favourites'
            ], 200);
        }

        // Add new favourite
        Favourites::create([
            'host_id' => $hostId,
            'business_id' => $businessId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to favourites'
        ], 201);
    }

    public function getFavouritesByHost()
    {
        try {
            $host = auth()->user()->id;
//            dd($host);
            // Validate request
          if(!$host){
              return response()->json(["message"=>"unauthorized"], 401);
          }

            // Load favourites with their business
            $favourites = Favourites::with('business')
                ->where('host_id', $host)
                ->get();

            // Remove favourites where business is missing (soft deleted or removed)
            $validFavourites = $favourites->filter(fn($fav) => $fav->business);

            // Extract business IDs
            $businessIds = $validFavourites->pluck('business_id')->toArray();

            // Fetch review stats (count + average)
            $reviewStats = Review::whereIn('business_id', $businessIds)
                ->selectRaw('business_id, COUNT(*) as review_count, AVG(points) as average_rating')
                ->groupBy('business_id')
                ->get()
                ->keyBy('business_id');

            // Fetch min package prices
            $packagePrices = Package::whereIn('business_id', $businessIds)
                ->selectRaw('business_id, MIN(price) as min_package_price')
                ->groupBy('business_id')
                ->get()
                ->keyBy('business_id');

            // Final response mapping
            $enrichedFavourites = $validFavourites->map(function ($fav) use ($reviewStats, $packagePrices) {

                $business = $fav->business;
                $businessId = $business->id;

                // Review stats fallback
                $stats = $reviewStats->get($businessId) ?? (object)[
                    'review_count' => 0,
                    'average_rating' => 0
                ];

                // Minimum package price
                $minPackage = $packagePrices->get($businessId)->min_package_price ?? null;

                // Minimum service price
                $minService = null;
                if (is_array($business->services) && !empty($business->services)) {
                    $servicePrices = collect($business->services)
                        ->pluck('price')
                        ->filter(fn($price) => is_numeric($price));

                    if ($servicePrices->isNotEmpty()) {
                        $minService = $servicePrices->min();
                    }
                }

                // Determine starting price
                $startingFrom = 0;

                if (!is_null($minPackage) && !is_null($minService)) {
                    $startingFrom = min($minPackage, $minService);
                } elseif (!is_null($minPackage)) {
                    $startingFrom = $minPackage;
                } elseif (!is_null($minService)) {
                    $startingFrom = $minService;
                }

                // Attach dynamic fields to business model
                $business->review_count = $stats->review_count;
                $business->average_rating = round($stats->average_rating, 2);
                $business->starting_from_price = $startingFrom;

                return [
                    'id' => $fav->id,
                    'host_id' => $fav->host_id,
                    'business_id' => $fav->business_id,
                    'business' => $business,
                    'created_at' => $fav->created_at,
                    'updated_at' => $fav->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Favourites fetched successfully',
                'favourites' => $enrichedFavourites,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
};
