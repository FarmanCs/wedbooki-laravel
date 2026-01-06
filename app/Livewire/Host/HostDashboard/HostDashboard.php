<?php

namespace App\Livewire\Host\HostDashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Booking;
use App\Models\Host\GuestGroup;
use App\Models\Host\HostPersonalizedChecklist;
use App\Models\Host\Checklist;
use App\Models\Vendor\Business;
use Carbon\Carbon;

#[Layout('components.layouts.host.host')]
#[Title('Dashboard')]
class HostDashboard extends Component
{
    public $host;

    // Stats
    public int $totalBookings = 0;
    public int $upcomingBookingsCount = 0;
    public int $guestGroupsCount = 0;
    public int $pendingTasksCount = 0;
    public int $favoritesCount = 0;
    public int $completedBookings = 0;

    // Collections
    public $upcomingBookings = [];
    public $pendingTasks = [];
    public $recentFavorites = [];
    public $recentChecklists = [];
    public $weddingTimeline = [];

    // Chart data
    public $bookingStats = [];

    public function mount(): void
    {
        $this->host = Auth::guard('host')->user();

        // Total bookings
        $this->totalBookings = Booking::where('host_id', $this->host->id)->count();

        // Upcoming bookings (next 30 days)
        $this->upcomingBookings = Booking::with(['business', 'vendor', 'package'])
            ->where('host_id', $this->host->id)
            ->whereDate('event_date', '>=', now())
            ->whereDate('event_date', '<=', now()->addDays(30))
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $this->upcomingBookingsCount = Booking::where('host_id', $this->host->id)
            ->whereDate('event_date', '>=', now())
            ->count();

        // Completed bookings
        $this->completedBookings = Booking::where('host_id', $this->host->id)
            ->whereDate('event_date', '<', now())
            ->where('status', 'completed')
            ->count();

        // Guest groups count
        $this->guestGroupsCount = GuestGroup::where('host_id', $this->host->id)->count();

        // Pending checklist tasks
        $this->pendingTasks = HostPersonalizedChecklist::where('host_id', $this->host->id)
            ->where('checklist_status', 'pending')
            ->where('check_list_due_date', '>=', now())
            ->orderBy('check_list_due_date')
            ->limit(5)
            ->get();

        $this->pendingTasksCount = $this->pendingTasks->count();

        // Favorites count
        $this->favoritesCount = \App\Models\Host\Favourites::where('host_id', $this->host->id)->count();

        // Recent favorites with business details
        $this->recentFavorites = \App\Models\Host\Favourites::with('business.category')
            ->where('host_id', $this->host->id)
            ->latest()
            ->limit(4)
            ->get();

        // Recent checklists
        $this->recentChecklists = Checklist::where('host_id', $this->host->id)
            ->latest()
            ->limit(3)
            ->get();

        // Wedding timeline (if wedding date exists)
        if ($this->host->wedding_date) {
            $this->calculateWeddingTimeline();
        }

        // Booking stats for chart
//        $this->calculateBookingStats();
    }

    private function calculateWeddingTimeline(): void
    {
        $weddingDate = Carbon::parse($this->host->wedding_date);
        $today = Carbon::now();
        $daysToWedding = $today->diffInDays($weddingDate, false);

        $this->weddingTimeline = [
            'days_to_wedding' => $daysToWedding > 0 ? $daysToWedding : 0,
            'wedding_date_formatted' => $weddingDate->format('F j, Y'),
            'is_past' => $daysToWedding < 0,
            'milestones' => [
                ['label' => '12 Months Before', 'date' => $weddingDate->copy()->subYear(), 'completed' => $today->gte($weddingDate->copy()->subYear())],
                ['label' => '6 Months Before', 'date' => $weddingDate->copy()->subMonths(6), 'completed' => $today->gte($weddingDate->copy()->subMonths(6))],
                ['label' => '3 Months Before', 'date' => $weddingDate->copy()->subMonths(3), 'completed' => $today->gte($weddingDate->copy()->subMonths(3))],
                ['label' => '1 Month Before', 'date' => $weddingDate->copy()->subMonth(), 'completed' => $today->gte($weddingDate->copy()->subMonth())],
                ['label' => 'Wedding Day', 'date' => $weddingDate, 'completed' => $today->gte($weddingDate)],
            ]
        ];
    }

//    private function calculateBookingStats(): void
//    {
//        $sixMonthsAgo = now()->subMonths(6);
//
//        $monthlyBookings = Booking::where('host_id', $this->host->id)
//            ->where('created_at', '>=', $sixMonthsAgo)
//            ->selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, COUNT(*) as count')
//            ->groupBy('month')
//            ->orderBy('created_at')
//            ->pluck('count', 'month');
//
//        $statusCounts = Booking::where('host_id', $this->host->id)
//            ->selectRaw('status, COUNT(*) as count')
//            ->groupBy('status')
//            ->pluck('count', 'status');
//
//        $this->bookingStats = [
//            'monthly' => $monthlyBookings,
//            'by_status' => $statusCounts,
//        ];
//    }

    public function markTaskComplete($taskId): void
    {
        $task = HostPersonalizedChecklist::where('host_id', $this->host->id)
            ->where('id', $taskId)
            ->first();

        if ($task) {
            $task->update(['checklist_status' => 'completed']);
            $this->pendingTasks = HostPersonalizedChecklist::where('host_id', $this->host->id)
                ->where('checklist_status', 'pending')
                ->where('check_list_due_date', '>=', now())
                ->orderBy('check_list_due_date')
                ->limit(5)
                ->get();

            $this->pendingTasksCount = HostPersonalizedChecklist::where('host_id', $this->host->id)
                ->where('checklist_status', 'pending')
                ->count();

            $this->dispatch('task-completed');
        }
    }

    public function render()
    {
        return view('livewire.Host.dashboard.host-dashboard');
    }
}
