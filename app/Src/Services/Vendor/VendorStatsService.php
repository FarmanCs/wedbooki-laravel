<?php

namespace App\Src\Services\Vendor;

use App\Models\Vendor\Vendor;
use App\Models\Vendor\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class VendorStatsService
{
    public function totalStats($id): JsonResponse
    {
        $vendor = Vendor::with('businessProfile')->find($id);

        if (!$vendor || !$vendor->businessProfile) {
            return response()->json(['error' => 'Vendor or business profile not found.'], 404);
        }

        $businessId = $vendor->businessProfile->id;
        $now = Carbon::now();

        // Weekly performance
        $weekPerformance = Booking::where('business_id', $businessId)
            ->where('status', 'accepted')
            ->where('created_at', '<=', $now)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, WEEK(created_at) as week, DAYOFWEEK(created_at) as dayOfWeek, SUM(amount) as revenue')
            ->groupBy('year', 'month', 'week', 'dayOfWeek')
            ->get();

        // Monthly performance
        $monthPerformance = Booking::where('business_id', $businessId)
            ->where('status', 'accepted')
            ->where('created_at', '<=', $now)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, DAY(created_at) as day, SUM(amount) as revenue')
            ->groupBy('year', 'month', 'day')
            ->get();

        // Yearly performance
        $yearPerformance = Booking::where('business_id', $businessId)
            ->where('status', 'accepted')
            ->where('created_at', '<=', $now)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as revenue')
            ->groupBy('year', 'month')
            ->get();

        // Top performing months
        $topMonths = Booking::where('business_id', $businessId)
            ->where('status', 'accepted')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as bookings, SUM(amount) as revenue')
            ->groupBy('year', 'month')
            ->orderByDesc('revenue')
            ->limit(6)
            ->get()
            ->map(function ($month) {
                return [
                    'month' => Carbon::create($month->year, $month->month)->format('F Y'),
                    'bookings' => $month->bookings,
                    'revenue' => $month->revenue,
                    'avgBookingValue' => $month->bookings > 0 ? $month->revenue / $month->bookings : 0,
                ];
            });

        // Total stats
        $totalStats = Booking::where('business_id', $businessId)
            ->where('status', 'accepted')
            ->selectRaw('COUNT(*) as totalBookings, SUM(amount) as totalRevenue')
            ->first();

        return response()->json([
            'performance' => [
                'week' => $weekPerformance,
                'month' => $monthPerformance,
                'year' => $yearPerformance,
            ],
            'topPerformingMonths' => $topMonths,
            'totalBookings' => $totalStats->totalBookings ?? 0,
            'totalRevenue' => $totalStats->totalRevenue ?? 0,
            'upcomingBookings' => 0, // Implement as needed
            'viewCount' => $vendor->businessProfile->view_count ?? 0,
            'socialCount' => $vendor->businessProfile->social_count ?? 0,
        ], 200);
    }

    public function topPerformingMonths($id, array $params): JsonResponse
    {
        $vendor = Vendor::with('businessProfile')->find($id);

        if (!$vendor || !$vendor->businessProfile) {
            return response()->json(['error' => 'Vendor or business profile not found.'], 404);
        }

        $businessId = $vendor->businessProfile->id;
        $selectedYear = isset($params['year']) ? (int)$params['year'] : null;

        $query = Booking::where('business_id', $businessId)
            ->where('status', 'accepted');

        if ($selectedYear) {
            $query->whereYear('created_at', $selectedYear);
        }

        $topMonths = $query->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as bookings, SUM(amount) as revenue')
            ->groupBy('year', 'month')
            ->having('bookings', '>=', 1)
            ->orderByDesc('revenue')
            ->limit(6)
            ->get()
            ->map(function ($month) {
                return [
                    'month' => Carbon::create($month->year, $month->month)->format('F Y'),
                    'revenue' => $month->revenue,
                    'bookings' => $month->bookings,
                    'avgBookingValue' => $month->bookings > 0 ? $month->revenue / $month->bookings : 0,
                ];
            });

        return response()->json(['topPerformingMonths' => $topMonths], 200);
    }

    public function getPackagePerformance($id, array $params): JsonResponse
    {
        $vendor = Vendor::with('businessProfile')->find($id);

        if (!$vendor || !$vendor->businessProfile) {
            return response()->json(['error' => 'Vendor or business profile not found.'], 404);
        }

        $businessId = $vendor->businessProfile->id;
        $year = $params['year'] ?? null;
        $month = $params['month'] ?? null;
        $week = $params['week'] ?? null;

        $query = Booking::where('business_id', $businessId)
            ->where('status', 'accepted');

        // Build date filter
        if ($year) {
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end = Carbon::create($year, 12, 31)->endOfYear();

            if ($month !== null) {
                $start = Carbon::create($year, (int)$month + 1, 1)->startOfMonth();
                $end = Carbon::create($year, (int)$month + 1, 1)->endOfMonth();

                if ($week !== null) {
                    $start = Carbon::create($year, (int)$month + 1, 1)->setISODate($year, (int)$week)->startOfWeek();
                    $end = $start->copy()->endOfWeek();
                }
            }

            $query->whereBetween('created_at', [$start, $end]);
        }

        $packagePerformance = $query->select('package_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as totalRevenue'))
            ->with('package:id,name,price')
            ->groupBy('package_id')
            ->get()
            ->map(function ($booking) {
                return [
                    'name' => $booking->package->name ?? 'Unknown',
                    'count' => $booking->count,
                    'totalRevenue' => $booking->totalRevenue,
                    'price' => $booking->package->price ?? 0,
                ];
            });

        return response()->json(['packagePerformance' => $packagePerformance], 200);
    }
}
