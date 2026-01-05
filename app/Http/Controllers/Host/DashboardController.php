<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $host = auth()->user();

        // Calculate all stats including budget and bookings
        $stats = [
            'favourite_vendors' => $host->favouriteBusinesses()->count(),
            'guest_groups' => $host->guestGroups()->count(),
            'checklist_items' => $host->checklists()->count(),
            'total_bookings' => $host->bookings()->count(),
            'budget_set' => !is_null($host->event_budget) && $host->event_budget > 0,
            'budget_amount' => $host->event_budget ?? 0,
        ];

        return view('livewire.dashboard.host-dashboard', [
            'host' => $host,
            'stats' => $stats,
        ]);
    }
}
