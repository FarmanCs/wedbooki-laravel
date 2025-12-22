<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /* ================================
     | Payment Callbacks (Public)
     |================================ */

    public function handlePlanPaymentSuccess(Request $request)
    {
        return response()->json([
            'message' => 'Plan payment successful',
            'data' => $request->all(),
        ]);
    }

    public function handlePlanPaymentFailure(Request $request)
    {
        return response()->json([
            'message' => 'Plan payment failed',
            'data' => $request->all(),
        ], 400);
    }

    public function handleCreditsPaymentSuccess(Request $request)
    {
        return response()->json([
            'message' => 'Credits payment successful',
            'data' => $request->all(),
        ]);
    }

    public function handleCreditsPaymentFailure(Request $request)
    {
        return response()->json([
            'message' => 'Credits payment failed',
            'data' => $request->all(),
        ], 400);
    }


    /* ================================
     | Checkout Sessions (Auth)
     |================================ */

    public function createPlanCheckoutSession(Request $request)
    {
        return response()->json([
            'message' => 'Plan checkout session created',
        ]);
    }

    public function createCreditsCheckoutSession(Request $request)
    {
        return response()->json([
            'message' => 'Credits checkout session created',
        ]);
    }


    /* ================================
     | Subscription Actions
     |================================ */

    public function boostProfile(Request $request)
    {
        return response()->json([
            'message' => 'Profile boosted successfully',
        ]);
    }

    public function cancelPlanSubscription(Request $request)
    {
        return response()->json([
            'message' => 'Plan subscription cancelled',
        ]);
    }


    /* ================================
     | Plans & Subscriptions
     |================================ */

    public function getAllPlans()
    {
        return response()->json([
            'message' => 'All plans fetched',
            'data' => [],
        ]);
    }

    public function getAllSubscriptions()
    {
        return response()->json([
            'message' => 'All subscriptions fetched',
            'data' => [],
        ]);
    }

    public function getAllDetailsOfPlan()
    {
        return response()->json([
            'message' => 'Plan details fetched',
            'data' => [],
        ]);
    }


    /* ================================
     | Transactions
     |================================ */

    public function getAllAdCreditsTransactions()
    {
        return response()->json([
            'message' => 'Ad credits transactions fetched',
            'data' => [],
        ]);
    }

    public function getAllBookingsTransactions()
    {
        return response()->json([
            'message' => 'Booking transactions fetched',
            'data' => [],
        ]);
    }


    /* ================================
     | Ad Credits Packages (CRUD)
     |================================ */

    public function storeAdCreditsPackage(Request $request)
    {
        return response()->json([
            'message' => 'Ad credits package created',
        ], 201);
    }

    public function updateAdCreditsPackage(Request $request, $package)
    {
        return response()->json([
            'message' => 'Ad credits package updated',
            'package_id' => $package,
        ]);
    }

    public function showAdCreditsPackage($package)
    {
        return response()->json([
            'message' => 'Ad credits package fetched',
            'package_id' => $package,
        ]);
    }

    public function getAllAdCreditsPackages()
    {
        return response()->json([
            'message' => 'All ad credits packages fetched',
            'data' => [],
        ]);
    }

    public function deleteAdCreditsPackage($package)
    {
        return response()->json([
            'message' => 'Ad credits package deleted',
            'package_id' => $package,
        ]);
    }
}
