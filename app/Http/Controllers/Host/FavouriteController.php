<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host\Review;
use App\Models\Vendor\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    public function addFavourite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business' => 'required|exists:businesses,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $host = auth()->user();
        $host->favouriteBusinesses->syncWithoutDetaching([$request->business]);


        $existing = Favourite::where('host_id', $request->host)
            ->where('business_id', $request->business)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from favourites'
            ]);
        } else {
            Favourite::create([
                'host_id' => $request->host,
                'business_id' => $request->business
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Added to favourites'
            ], 201);
        }
    }

    public function getFavouritesByHost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required|exists:hosts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $favourites = Favo::with('business')
            ->where('host_id', $request->host)
            ->get();

        $validFavourites = $favourites->filter(function ($fav) {
            return $fav->business;
        });

        $businessIds = $validFavourites->pluck('business.id');

        // Get review stats
        $reviewStats = Review::whereIn('business_id', $businessIds)
            ->selectRaw('business_id, COUNT(*) as review_count, AVG(points) as average_rating')
            ->groupBy('business_id')
            ->get()
            ->keyBy('business_id');

        // Get package prices
        $packagePrices = Package::whereIn('business_id', $businessIds)
            ->selectRaw('business_id, MIN(price) as min_package_price')
            ->groupBy('business_id')
            ->get()
            ->keyBy('business_id');

        $enrichedFavourites = $validFavourites->map(function ($fav) use ($reviewStats, $packagePrices) {
            $business = $fav->business;
            $businessId = $business->id;

            $stats = $reviewStats->get($businessId) ?? (object) [
                'review_count' => 0,
                'average_rating' => 0
            ];

            $minPackagePrice = $packagePrices->get($businessId)->min_package_price ?? null;

            $minServicePrice = null;
            if (!empty($business->services)) {
                $servicePrices = collect($business->services)
                    ->pluck('price')
                    ->filter(function ($price) {
                        return is_numeric($price);
                    });
                if ($servicePrices->isNotEmpty()) {
                    $minServicePrice = $servicePrices->min();
                }
            }

            $startingFromPrice = 0;
            if (!is_null($minPackagePrice)) {
            $startingFromPrice = $minPackagePrice;
        }
            if (!is_null($minServicePrice)) {
            $startingFromPrice = is_null($minPackagePrice) ? $minServicePrice : min($minPackagePrice, $minServicePrice);
        }

            $business->review_count = $stats->review_count;
            $business->average_rating = round($stats->average_rating, 2);
            $business->starting_from_price = $startingFromPrice;

            return [
                'id' => $fav->id,
                'host_id' => $fav->host_id,
                'business_id' => $fav->business_id,
                'business' => $business,
                'created_at' => $fav->created_at,
                'updated_at' => $fav->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Favourites fetched successfully',
            'favourites' => $enrichedFavourites
        ]);
    }
};
