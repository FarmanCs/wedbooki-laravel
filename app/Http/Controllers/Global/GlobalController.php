<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Business;
use App\Models\BusinessSocialClick;
use App\Models\BusinessView;
use App\Models\Vendor\Category;
use App\Models\Country;
use App\Models\SupportQuery;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GlobalController extends Controller
{
    public function GetAllVendors(Request $request)
    {
        try {
            $category = $request->query('category');

            $query = Business::whereNotNull('company_name')
                ->where('company_name', '!=', '')
                ->whereNotNull('business_desc')
                ->where('business_desc', '!=', '')
                ->whereJsonLength('portfolio_images', '>', 0);

            if ($category) {
                $categoryDoc = Category::where('type', trim($category))->first();

                if (!$categoryDoc) {
                    return response()->json(['message' => 'Category not found'], 404);
                }

                $query->where('category_id', $categoryDoc->id);
            }

            $vendors = $query->with(['reviews', 'packages', 'extraServices'])->get()->map(function ($vendor) {
                $minPackagePrice = $vendor->packages->min('price');
                $minServicePrice = $vendor->extraServices->min('price');

                $startingFromPrice = $minPackagePrice !== null && $minServicePrice !== null
                    ? min($minPackagePrice, $minServicePrice)
                    : ($minPackagePrice ?? $minServicePrice ?? 0);

                $reviewCount = $vendor->reviews->count();
                $averageRating = $reviewCount > 0 ? $vendor->reviews->avg('points') : 0;

                // Only unset fields if they exist
                foreach (['password', 'timings_venue', 'timings_service_weekly', 'working_hours', 'payout_info'] as $field) {
                    if (isset($vendor->$field)) unset($vendor->$field);
                }

                return array_merge($vendor->toArray(), [
                    'startingFromPrice' => $startingFromPrice,
                    'reviewCount' => $reviewCount,
                    'averageRating' => round($averageRating, 2)
                ]);
            });

            return response()->json(['vendors' => $vendors], 200);

        } catch (\Exception $e) {
            \Log::error('GetAllVendors Error: ' . $e->getMessage());
            return response()->json(['message' => 'Please try again later.'], 500);
        }
    }


    public function SearchAllVendors(Request $request)
    {
        try {
            $search = $request->query('search');

            $query = Business::whereNotNull('company_name')
                ->where('company_name', '!=', '')
                ->whereNotNull('business_desc')
                ->where('business_desc', '!=', '')
                ->whereHas('portfolioImages')
                ->with('category');

            if ($search) {
                $searchTerm = trim($search);

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('company_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('business_desc', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('city', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('country', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                            $catQuery->where('type', 'LIKE', "%{$searchTerm}%");
                        });
                });

                // Add scoring for relevance
                $vendors = $query->get()->map(function ($vendor) use ($searchTerm) {
                    $score = 0;
                    if (stripos($vendor->company_name, $searchTerm) !== false) {
                        $score = 5;
                    } elseif ($vendor->category && stripos($vendor->category->type, $searchTerm) !== false) {
                        $score = 4;
                    } elseif (stripos($vendor->business_desc, $searchTerm) !== false) {
                        $score = 3;
                    } elseif (stripos($vendor->city, $searchTerm) !== false) {
                        $score = 2;
                    } elseif (stripos($vendor->country, $searchTerm) !== false) {
                        $score = 1;
                    }

                    return [
                        '_id' => $vendor->id,
                        'company_name' => $vendor->company_name,
                        'city' => $vendor->city,
                        'country' => $vendor->country,
                        'portfolio_images' => $vendor->portfolio_images,
                        'category' => $vendor->category->type ?? null,
                        'score' => $score
                    ];
                })->sortByDesc('score')->take(20)->values();
            } else {
                $vendors = $query->take(20)->get()->map(function ($vendor) {
                    return [
                        '_id' => $vendor->id,
                        'company_name' => $vendor->company_name,
                        'city' => $vendor->city,
                        'country' => $vendor->country,
                        'portfolio_images' => $vendor->portfolio_images,
                        'category' => $vendor->category->type ?? null,
                    ];
                });
            }

            return response()->json(['vendors' => $vendors], 200);
        } catch (\Exception $e) {
            \Log::error('SearchAllVendors Error: ' . $e->getMessage());
            return response()->json(['message' => 'Please try again later.'], 500);
        }
    }

    public function GetTopRatedVendors(Request $request)
    {
        try {
            $vendors = Business::with([
                'reviews',
                'bookings',
                'vendor' => function ($query) {
                    $query->select([
                        'id',
                        'full_name',
                        'email',
                        'phone_no',
                        'country',
                        'city',
                        'role',
                        'profile_image',
                        'years_of_experince',
                        'languages',
                        'team_members',
                        'specialties',
                        'about',
                        'business_profile_id',
                        'profile_verification',
                        'email_verified',
                        'cover_image',
                        'last_login',
                        'custom_vendor_id',
                        'signup_method',
                        'google_id',
                        'payout_info'
                    ]);
                }
            ])->get()->map(function ($vendor) {
                $reviewCount = $vendor->reviews->count();
                $averageRating = $reviewCount > 0 ? $vendor->reviews->avg('points') : 0;
                $bookingCount = $vendor->bookings->count();

                return [
                    'id' => $vendor->id,
                    'company_name' => $vendor->company_name,
                    'city' => $vendor->city,
                    'country' => $vendor->country,
                    'averageRating' => round($averageRating, 2),
                    'bookingCount' => $bookingCount,
                    'portfolio_images' => $vendor->portfolio_images,
                    'business_desc' => $vendor->business_desc,
                    'vendorDetails' => $vendor->vendor
                ];
            })->where('averageRating', '>', 2.5)
                ->sortByDesc('averageRating')
                ->sortByDesc('bookingCount')
                ->values();

            return response()->json([
                'message' => 'Top rated vendors',
                'vendors' => $vendors
            ], 200);
        } catch (\Exception $e) {
            \Log::error('GetTopRatedVendors Error: ' . $e->getMessage());
            return response()->json(['message' => 'Please try again later.', 'error' => $e->getMessage()], 500);
        }
    }

    public function SearchVendorsByCategory(Request $request)
    {
        try {
            $category = $request->query('category');

            if (!$category) {
                return response()->json(['message' => 'Category is required.'], 400);
            }

            $query = Business::whereNotNull('company_name')
                ->where('company_name', '!=', '')
                ->whereHas('portfolioImages')
                ->with('category');

            if (strtolower($category) === 'vendor') {
                $query->whereHas('category', function ($q) {
                    $q->where('type', '!=', 'venue');
                });
            } else {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('type', 'LIKE', '%' . trim($category) . '%');
                });
            }

            $vendors = $query->take(20)->get()->map(function ($vendor) {
                return [
                    '_id' => $vendor->id,
                    'company_name' => $vendor->company_name,
                    'image' => $vendor->portfolio_images[0] ?? null,
                    'category' => $vendor->category->type ?? null,
                ];
            });

            return response()->json(['vendors' => $vendors], 200);
        } catch (\Exception $e) {
            \Log::error('SearchVendorsByCategory Error: ' . $e->getMessage());
            return response()->json(['message' => 'Please try again later.'], 500);
        }
    }

    public function SingleVendor(Request $request, $id)
    {
        try {
            $business = Business::with([
                'reviews.host:id,full_name',
                'reviews.vendorReplies.vendor:id,full_name',
                'packages',
                'category',
                'services'
            ])->find($id);

            if (!$business) {
                return response()->json(['message' => 'Vendor not found'], 404);
            }

            // Find the vendor who owns this business
            $vendor = Vendor::where('business_profile_id', $id)
                ->select([
                    'id',
                    'specialties',
                    'years_of_experince',
                    'full_name',
                    'languages',
                    'team_members',
                    'about',
                    'profile_image'
                ])->first();

            // Profile completeness logic
            $requiredFields = [
                'company_name',
                'profile_image',
                'cover_image',
                'business_desc',
                'business_license_number',
                'portfolio_images'
            ];

            $filled = 0;
            foreach ($requiredFields as $field) {
                $value = $business->{$field};
                if (is_array($value)) {
                    if (count($value) > 0) $filled++;
                } elseif (is_string($value)) {
                    if (trim($value) !== '') $filled++;
                } elseif ($value) {
                    $filled++;
                }
            }

            $profileCompleteness = round(($filled / count($requiredFields)) * 100);

            // Calculate average rating and review count
            $reviews = $business->reviews ?? collect();
            $reviewCount = $reviews->count();

            $averageRating = 0;
            if ($reviewCount > 0) {
                $totalPoints = $reviews->sum('points');
                $averageRating = $totalPoints / $reviewCount;
            }

            // Calculate min price
            $packagePrices = $business->packages->pluck('price')->filter(function ($price) {
                return is_numeric($price);
            });
            $servicePrices = $business->services->pluck('price')->filter(function ($price) {
                return is_numeric($price);
            });

            $minPackagePrice = $packagePrices->count() > 0 ? $packagePrices->min() : null;
            $minServicePrice = $servicePrices->count() > 0 ? $servicePrices->min() : null;

            $startingFromPrice = 0;
            if ($minPackagePrice !== null && $minServicePrice !== null) {
                $startingFromPrice = min($minPackagePrice, $minServicePrice);
            } elseif ($minPackagePrice !== null) {
                $startingFromPrice = $minPackagePrice;
            } elseif ($minServicePrice !== null) {
                $startingFromPrice = $minServicePrice;
            }

            // Remove sensitive data
            unset($business->password);
            unset($business->payout_info);

            // Add computed fields
            $businessData = $business->toArray();
            $businessData['profileCompleteness'] = $profileCompleteness;
            $businessData['averageRating'] = round($averageRating, 1);
            $businessData['reviewCount'] = $reviewCount;
            $businessData['startingFromPrice'] = $startingFromPrice;

            if ($vendor) {
                $businessData['vendorId'] = $vendor->id;
                $businessData['vendorPersonalDetail'] = $vendor;
            }

            return response()->json([
                'message' => 'Vendor fetched successfully',
                'business' => $businessData
            ], 200);
        } catch (\Exception $e) {
            \Log::error('SingleVendor Error: ' . $e->getMessage());
            return response()->json(['message' => 'Please try again later.'], 500);
        }
    }

    public function ViewProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'businessId' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Missing deviceId or businessId'], 400);
            }


            $businessId = $request->input('businessId');

            $rawUserAgent = $request->header('User-Agent', '');
            $userAgent = strtolower(trim($rawUserAgent));

            // Normalize IP
            $ip = $request->header('X-Forwarded-For', $request->ip());
            $ip = explode(',', $ip)[0];
            $ip = trim($ip);

            // Generate device ID
            $deviceId = hash('sha256', $userAgent . $ip);

            if (!$deviceId || !$businessId) {
                return response()->json(['error' => 'Missing deviceId or businessId'], 400);
            }

            $alreadyViewed = BusinessView::where('business_id', $businessId)
                ->where('device_id', $deviceId)
                ->first();

            if (!$alreadyViewed) {
                BusinessView::create([
                    'business_id' => $businessId,
                    'device_id' => $deviceId
                ]);

                Business::where('id', $businessId)->increment('view_count');
            }

            return response()->json([
                'message' => $alreadyViewed ? 'Already viewed' : 'View recorded'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('View error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function TrackSocialClick(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'businessId' => 'required',
                'platform' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Missing required data'], 400);
            }

            $businessId = $request->input('businessId');
            $platform = strtolower($request->input('platform'));
            $userAgent = $request->header('User-Agent', '');
            $ip = $request->ip();

            // Generate device ID
            $deviceId = hash('sha256', $userAgent . $ip);

            $alreadyClicked = BusinessSocialClick::where('business_id', $businessId)
                ->where('device_id', $deviceId)
                ->where('platform', $platform)
                ->first();

            if (!$alreadyClicked) {
                BusinessSocialClick::create([
                    'business_id' => $businessId,
                    'device_id' => $deviceId,
                    'platform' => $platform
                ]);

                Business::where('id', $businessId)->increment('social_count');
            }

            return response()->json(['message' => 'Social click recorded'], 200);
        } catch (\Exception $e) {
            \Log::error('Social click error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function submitQuery(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string',
                'email' => 'required|email',
                'phoneNumber' => 'required|string',
                'subject' => 'required|string',
                'message' => 'required|string',
                'priority' => 'nullable|string',
                'attachments' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // Check for existing pending query
            $existingPending = SupportQuery::where('email', $request->input('email'))
                ->where('status', 'pending')
                ->first();

            if ($existingPending) {
                return response()->json([
                    'error' => 'You already have a pending query. Please wait for a response.'
                ], 400);
            }

            $newQuery = SupportQuery::create([
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phoneNumber'),
                'subject' => $request->input('subject'),
                'priority' => $request->input('priority'),
                'message' => $request->input('message'),
                'attachments' => $request->input('attachments', [])
            ]);

            return response()->json([
                'message' => 'Query submitted successfully',
                'query' => $newQuery
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Support query error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function addCountry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'countryName' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'countryName required'], 400);
            }

            $countryName = $request->input('countryName');

            $doc = Country::first();
            if (!$doc) {
                $doc = new Country();
                $doc->countries = [];
            }

            if (in_array($countryName, $doc->countries ?? [])) {
                return response()->json(['error' => 'Country already exists'], 400);
            }

            $countries = $doc->countries ?? [];
            $countries[] = $countryName;
            $doc->countries = $countries;
            $doc->save();

            return response()->json([
                'message' => 'Country added',
                'countries' => $doc->countries
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Add Country Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getCountries(Request $request)
    {
        try {
            $doc = Country::first();
            if (!$doc) {
                return response()->json(['countries' => []], 200);
            }

            return response()->json(['countries' => $doc->countries ?? []], 200);
        } catch (\Exception $e) {
            \Log::error('Get Countries Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function deleteCountry(Request $request, $name)
    {
        try {
            $doc = Country::first();
            if (!$doc) {
                return response()->json(['error' => 'No countries found'], 404);
            }

            $countries = $doc->countries ?? [];
            if (!in_array($name, $countries)) {
                return response()->json(['error' => 'Country not found in array'], 404);
            }

            $doc->countries = array_values(array_filter($countries, function ($c) use ($name) {
                return strtolower($c) !== strtolower($name);
            }));
            $doc->save();

            return response()->json([
                'message' => 'Country deleted',
                'countries' => $doc->countries
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Delete Country Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
