<?php

namespace App\Livewire\Host\Vendors;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Business;
use App\Models\Host\Favourites;
use App\Models\Admin\Category;

#[Layout('components.layouts.app')]
#[Title('Browse Vendors')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $categoryFilter = null;
    public string $sortBy = 'rating';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'categoryFilter', 'sortBy'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function toggleFavourite(int $businessId): void
    {
        $hostId = Auth::guard('host')->id();

        $favourite = Favourites::where('host_id', $hostId)
            ->where('business_id', $businessId)
            ->first();

        if ($favourite) {
            $favourite->delete();
            session()->flash('success', 'Removed from favourites.');
        } else {
            Favourites::create([
                'host_id' => $hostId,
                'business_id' => $businessId,
            ]);
            session()->flash('success', 'Added to favourites.');
        }
    }

    public function isFavourite(int $businessId): bool
    {
        return Favourites::where('host_id', Auth::guard('host')->id())
            ->where('business_id', $businessId)
            ->exists();
    }

    public function render()
    {
        $businesses = Business::with(['category', 'vendor', 'packages'])
            ->when($this->search, function ($query) {
                $query->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('business_desc', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $categories = Category::orderBy('name')->get();

        // Get favourite business IDs for current host
        $favouriteIds = Favourites::where('host_id', Auth::guard('host')->id())
            ->pluck('business_id')
            ->toArray();

        return view('livewire.host.vendors.index', [
            'businesses' => $businesses,
            'categories' => $categories,
            'favouriteIds' => $favouriteIds,
        ]);
    }
}
