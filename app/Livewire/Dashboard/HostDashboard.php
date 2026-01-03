<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HostDashboard extends Component
{
    public $host;

    public function mount()
    {
        $this->host = Auth::guard('host')->user();

        if (!$this->host) {
            $this->redirect(route('host.host-login'), navigate: true);
        }
    }

    public function render()
    {
        $stats = [
            'total_bookings'    => $this->host->checklists()->count(),
            'favourite_vendors' => $this->host->favourites()->count(),
            'guest_groups'      => $this->host->guestGroups()->count(),
            'checklist_items'   => $this->host->checklists()->count(),
        ];

        return view('livewire.dashboard.host-dashboard', compact('stats'))
            ->layout('components.layouts.host', ['title' => 'Host Dashboard']);
    }
}
