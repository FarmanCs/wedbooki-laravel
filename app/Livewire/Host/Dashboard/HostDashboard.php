<?php

namespace App\Livewire\Host\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Vendor\Booking;

class HostDashboard extends Component
{
    public $host;
    public $stats = [];
    public $upcomingEvents = [];
    public $recentBookings = [];
    public $budgetInfo = [];
    public $guestSummary = [];
    public $recentActivity = [];
    public $loading = true;

    // Real-time notification count
    public $notificationCount = 0;

    // Filter properties
    public $eventFilter = 'all'; // all, upcoming, past
    public $dateRange = 'week'; // week, month, all

    protected $listeners = [
        'bookingUpdated' => 'refreshData',
        'guestResponseReceived' => 'refreshData',
    ];



    public function mount()
    {
        $this->host = Auth::guard('host')->user();

        if (!$this->host) {
            return $this->redirect(route('host.host-login'), navigate: true);
        }

        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->loading = true;

        $this->getStats();
        $this->getBudgetSummary();
        $this->getUpcomingEvents();
        $this->getRecentBookings();
        $this->getGuestSummary();
        $this->getRecentActivity();
        $this->getNotificationCount();

        $this->loading = false;
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('data-refreshed');
    }

    public function getStats()
    {
        $this->stats = [
            'total_bookings' => $this->host->bookings()->count(),
            'pending_bookings' => $this->host->bookings()->where('status', 'pending')->count(),
            'confirmed_bookings' => $this->host->bookings()->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $this->host->bookings()->where('status', 'cancelled')->count(),
            'favourite_vendors' => $this->host->favourites()->count(),
            'guest_groups' => $this->host->guestGroups()->count(),
            'total_guests' => $this->host->guestGroups()->withCount('guests')->get()->sum('guests_count'),
            'confirmed_guests' => $this->getTotalConfirmedGuests(),
            'checklist_items' => $this->host->personalizedChecklists()->count(),
            'completed_checklist_items' => $this->host->personalizedChecklists()->where('checklist_status', 'completed')->count(),
            'wedding_date' => $this->host->wedding_date ? Carbon::parse($this->host->wedding_date)->format('F j, Y') : null,
            'days_until_wedding' => $this->host->wedding_date ? Carbon::now()->diffInDays(Carbon::parse($this->host->wedding_date), false) : null,
            'weeks_until_wedding' => $this->host->wedding_date ? Carbon::now()->diffInWeeks(Carbon::parse($this->host->wedding_date), false) : null,
            'event_budget' => $this->host->event_budget ?? 0,
            'estimated_guests' => $this->host->estimated_guests ?? 0,
        ];
    }

    public function getTotalConfirmedGuests()
    {
        $total = 0;
        foreach ($this->host->guestGroups as $group) {
            $total += $group->guests()->where('is_attending', 'yes')->count();
        }
        return $total;
    }

    public function getBudgetSummary()
    {
        $totalBudget = $this->host->event_budget ?? 0;

        // Use 'amount' column instead of 'total_amount'
        $spentAmount = $this->host->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('amount');

        $remainingBudget = $totalBudget - $spentAmount;
        $percentageSpent = $totalBudget > 0 ? ($spentAmount / $totalBudget) * 100 : 0;

        // Budget categories breakdown (assuming vendor_category exists in bookings)
        $categoryBreakdown = $this->host->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->selectRaw('business_id, SUM(amount) as total')
            ->groupBy('business_id')
            ->with(['business.category'])
            ->get()
            ->mapWithKeys(function ($item) {
                $categoryName = $item->business->category->type ?? 'General';
                return [$categoryName => ($categoryBreakdown[$categoryName] ?? 0) + $item->total];
            })
            ->toArray();

        $this->budgetInfo = [
            'total_budget' => $totalBudget,
            'spent_amount' => $spentAmount,
            'remaining_budget' => max(0, $remainingBudget),
            'percentage_spent' => round(min(100, $percentageSpent), 2),
            'category_breakdown' => $categoryBreakdown,
            'budget_status' => $this->getBudgetStatus($percentageSpent),
            'average_booking_cost' => $this->host->bookings()->whereIn('status', ['confirmed', 'completed'])->avg('amount') ?? 0,
        ];
    }

    private function getBudgetStatus($percentage)
    {
        if ($percentage >= 90) return 'critical';
        if ($percentage >= 75) return 'warning';
        if ($percentage >= 50) return 'moderate';
        return 'good';
    }

    public function getUpcomingEvents()
    {
        $query = $this->host->bookings()
            ->with(['business', 'business.category'])
            ->where('event_date', '>=', Carbon::today());

        if ($this->dateRange === 'week') {
            $query->where('event_date', '<=', Carbon::now()->addWeek());
        } elseif ($this->dateRange === 'month') {
            $query->where('event_date', '<=', Carbon::now()->addMonth());
        }

        $this->upcomingEvents = $query
            ->orderBy('event_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'vendor_name' => $booking->business->company_name ?? 'N/A',
                    'vendor_category' => $booking->business->category->type ?? 'General',
                    'date' => Carbon::parse($booking->event_date)->format('M d, Y'),
                    'time' => $booking->start_time ? Carbon::parse($booking->start_time)->format('h:i A') : 'N/A',
                    'status' => $booking->status,
                    'amount' => $booking->amount,
                    'days_away' => Carbon::parse($booking->event_date)->diffInDays(Carbon::now()),
                    'type' => $booking->package_id ? 'Package' : 'Custom',
                ];
            });
    }

    public function getRecentBookings()
    {
        $this->recentBookings = $this->host->bookings()
            ->with(['business', 'business.category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'vendor_name' => $booking->business->company_name ?? 'N/A',
                    'vendor_category' => $booking->business->category->type ?? 'General',
                    'booking_date' => Carbon::parse($booking->created_at)->format('M d, Y'),
                    'event_date' => Carbon::parse($booking->event_date)->format('M d, Y'),
                    'status' => $booking->status,
                    'status_color' => $this->getStatusColor($booking->status),
                    'amount' => $booking->amount,
                    'created_at' => $booking->created_at->diffForHumans(),
                    'payment_status' => $booking->payment_status,
                ];
            });
    }

    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            'declined' => 'secondary',
        ];

        return $colors[$status] ?? 'secondary';
    }

    public function getGuestSummary()
    {
        $groups = $this->host->guestGroups()->withCount('guests')->get();

        $totalConfirmed = $this->getTotalConfirmedGuests();
        $totalDeclined = $this->getTotalDeclinedGuests();
        $totalInvited = $groups->sum('guests_count');
        $awaitingResponse = $this->getTotalAwaitingGuests();

        $this->guestSummary = [
            'total_groups' => $groups->count(),
            'total_invited' => $totalInvited,
            'total_confirmed' => $totalConfirmed,
            'total_declined' => $totalDeclined,
            'awaiting_response' => $awaitingResponse,
            'response_rate' => $totalInvited > 0 ? round((($totalConfirmed + $totalDeclined) / $totalInvited) * 100, 1) : 0,
            'confirmation_rate' => $totalInvited > 0 ? round(($totalConfirmed / $totalInvited) * 100, 1) : 0,
        ];
    }

    public function getTotalDeclinedGuests()
    {
        $total = 0;
        foreach ($this->host->guestGroups as $group) {
            $total += $group->guests()->where('is_attending', 'no')->count();
        }
        return $total;
    }

    public function getTotalAwaitingGuests()
    {
        $total = 0;
        foreach ($this->host->guestGroups as $group) {
            $total += $group->guests()
                ->where(function ($query) {
                    $query->where('is_attending', 'pending')
                        ->orWhereNull('is_attending');
                })
                ->count();
        }
        return $total;
    }

    public function getRecentActivity()
    {
        $activities = collect();

        // Recent bookings
        $this->host->bookings()
            ->latest()
            ->take(3)
            ->with('business')
            ->get()
            ->each(function ($booking) use ($activities) {
                $activities->push([
                    'type' => 'booking',
                    'icon' => 'calendar',
                    'title' => 'New ' . ucfirst($booking->status) . ' Booking',
                    'description' => $booking->business->company_name ?? 'Vendor booking',
                    'time' => $booking->created_at->diffForHumans(),
                    'timestamp' => $booking->created_at,
                    'color' => $this->getStatusColor($booking->status),
                ]);
            });

        // Guest responses
        $guestGroups = $this->host->guestGroups()->with('guests')->get();
        foreach ($guestGroups as $group) {
            $group->guests()
                ->whereNotNull('rsvp_response_at')
                ->latest('rsvp_response_at')
                ->take(2)
                ->get()
                ->each(function ($guest) use ($activities, $group) {
                    $activities->push([
                        'type' => 'rsvp',
                        'icon' => 'user-check',
                        'title' => 'Guest RSVP Response',
                        'description' => $guest->first_name . ' ' . $guest->last_name . ' from ' . $group->group_name,
                        'time' => Carbon::parse($guest->rsvp_response_at)->diffForHumans(),
                        'timestamp' => $guest->rsvp_response_at,
                        'color' => $guest->is_attending === 'yes' ? 'success' : 'danger',
                    ]);
                });
        }

        // Checklist updates
        $this->host->personalizedChecklists()
            ->latest()
            ->take(2)
            ->get()
            ->each(function ($checklist) use ($activities) {
                $activities->push([
                    'type' => 'checklist',
                    'icon' => 'check-square',
                    'title' => 'Checklist Item ' . ucfirst($checklist->checklist_status),
                    'description' => $checklist->check_list_description,
                    'time' => $checklist->updated_at->diffForHumans(),
                    'timestamp' => $checklist->updated_at,
                    'color' => $checklist->checklist_status === 'completed' ? 'success' : 'primary',
                ]);
            });

        $this->recentActivity = $activities
            ->sortByDesc('timestamp')
            ->take(8)
            ->values()
            ->toArray();
    }

    public function getNotificationCount()
    {
        $pendingBookings = $this->host->bookings()->where('status', 'pending')->count();
        $awaitingRSVP = $this->guestSummary['awaiting_response'] ?? 0;

        $this->notificationCount = $pendingBookings + $awaitingRSVP;
    }

    public function updateEventFilter($filter)
    {
        $this->eventFilter = $filter;
        $this->getUpcomingEvents();
    }

    public function updateDateRange($range)
    {
        $this->dateRange = $range;
        $this->getUpcomingEvents();
    }

    public function render()
    {
        return view('livewire.host.dashboard.host-dashboard', [
            'stats' => $this->stats,
            'budgetInfo' => $this->budgetInfo,
            'upcomingEvents' => $this->upcomingEvents,
            'recentBookings' => $this->recentBookings,
            'guestSummary' => $this->guestSummary,
            'recentActivity' => $this->recentActivity,
            'loading' => $this->loading,
        ])->layout('components.layouts.host', ['title' => 'Dashboard - Wedding Host']);
    }
}
