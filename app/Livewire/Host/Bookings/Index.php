<?php

namespace App\Livewire\Host\Bookings;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Booking;

#[Layout('components.layouts.app')]
#[Title('Bookings')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $sortBy = 'event_date';
    public string $sortDirection = 'asc';

    protected $queryString = ['search', 'statusFilter', 'sortBy', 'sortDirection'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteBooking(int $bookingId): void
    {
        $booking = Booking::where('id', $bookingId)
            ->where('host_id', Auth::guard('host')->id())
            ->first();

        if ($booking) {
            $booking->delete();
            session()->flash('success', 'Booking deleted successfully.');
        }
    }

    public function render()
    {
        $bookings = Booking::where('host_id', Auth::guard('host')->id())
            ->with(['business', 'vendor', 'package'])
            ->when($this->search, function ($query) {
                $query->whereHas('business', function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.host.bookings.index', [
            'bookings' => $bookings,
        ]);
    }
}
