<?php

namespace App\Livewire\Host\Guests;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Host\Guest;
use App\Models\Host\GuestGroup;

#[Layout('components.layouts.app')]
#[Title('Guests')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $groupFilter = null;
    public bool $showModal = false;

    // Guest form fields
    public ?int $guestId = null;
    public string $full_name = '';
    public string $email = '';
    public string $phone_no = '';
    public string $mobile_no = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $zipcode = '';
    public bool $is_joining = true;
    public array $selectedGroups = [];

    protected $queryString = ['search', 'groupFilter'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['nullable', 'string', 'max:20'],
            'is_joining' => ['boolean'],
            'selectedGroups' => ['array'],
        ];
    }

    public function openModal(?int $guestId = null): void
    {
        $this->resetForm();

        if ($guestId) {
            $guest = Guest::findOrFail($guestId);
            $this->guestId = $guest->id;
            $this->full_name = $guest->full_name;
            $this->email = $guest->email ?? '';
            $this->phone_no = $guest->phone_no ?? '';
            $this->mobile_no = $guest->mobile_no ?? '';
            $this->address = $guest->address ?? '';
            $this->city = $guest->city ?? '';
            $this->state = $guest->state ?? '';
            $this->zipcode = $guest->zipcode ?? '';
            $this->is_joining = $guest->is_joining;
            $this->selectedGroups = $guest->groups->pluck('id')->toArray();
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveGuest(): void
    {
        $validated = $this->validate();

        $guest = Guest::updateOrCreate(
            ['id' => $this->guestId],
            [
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone_no' => $validated['phone_no'],
                'mobile_no' => $validated['mobile_no'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zipcode' => $validated['zipcode'],
                'is_joining' => $validated['is_joining'],
            ]
        );

        // Sync groups
        $guest->groups()->sync($this->selectedGroups);

        session()->flash('success', $this->guestId ? 'Guest updated successfully.' : 'Guest created successfully.');
        $this->closeModal();
    }

    public function deleteGuest(int $guestId): void
    {
        $guest = Guest::findOrFail($guestId);
        $guest->delete();

        session()->flash('success', 'Guest deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->guestId = null;
        $this->full_name = '';
        $this->email = '';
        $this->phone_no = '';
        $this->mobile_no = '';
        $this->address = '';
        $this->city = '';
        $this->state = '';
        $this->zipcode = '';
        $this->is_joining = true;
        $this->selectedGroups = [];
        $this->resetValidation();
    }

    public function render()
    {
        $hostId = Auth::guard('host')->id();

        $guests = Guest::whereHas('groups', function($query) use ($hostId) {
            $query->where('host_id', $hostId);
        })
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->groupFilter, function ($query) {
                $query->whereHas('groups', function($q) {
                    $q->where('guest_groups.id', $this->groupFilter);
                });
            })
            ->with('groups')
            ->paginate(10);

        $groups = GuestGroup::where('host_id', $hostId)->get();

        return view('livewire.host.guests.index', [
            'guests' => $guests,
            'groups' => $groups,
        ]);
    }
}
