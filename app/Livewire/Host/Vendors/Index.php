<?php

namespace App\Livewire\Host\Vendors;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Business;
use App\Models\Host\Favourites;
use App\Models\Admin\Category;
use Illuminate\Support\Str;

#[Layout('components.layouts.host.host')]
#[Title('Browse Vendors')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $categoryFilter = null;
    public array $carouselPositions = [];
    public array $carouselDotPositions = []; // New: Track dot positions
    public int $perPage = 25;

    // Maximum number of cards to show before enabling carousel
    private const MAX_CARDS_BEFORE_CAROUSEL = 4;
    private const CARDS_PER_VIEW = 4;
    private const CAROUSEL_STEP = 4; // New: Move by 4 cards at a time

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'categoryFilter' => ['except' => null, 'as' => 'category'],
        'perPage' => ['except' => 25, 'as' => 'per_page'],
    ];

    public function mount()
    {
        if (request()->has('per_page')) {
            $limit = (int) request('per_page');
            if (in_array($limit, [25, 50, 75, 100])) {
                $this->perPage = $limit;
            }
        }

        $categories = Category::all();
        foreach ($categories as $category) {
            $this->carouselPositions[$category->id] = 0;
            $this->carouselDotPositions[$category->id] = 0; // Initialize dot positions
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
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
        } else {
            Favourites::create([
                'host_id' => $hostId,
                'business_id' => $businessId,
            ]);
        }
    }

    public function nextCarousel(int $categoryId)
    {
        if (!isset($this->carouselPositions[$categoryId])) {
            $this->carouselPositions[$categoryId] = 0;
            $this->carouselDotPositions[$categoryId] = 0;
        }

        $businessCount = $this->getBusinessCountForCategory($categoryId);
        $maxPosition = max(0, $businessCount - self::CARDS_PER_VIEW);
        $maxDots = max(0, ceil($businessCount / self::CARDS_PER_VIEW) - 1);

        if ($this->carouselPositions[$categoryId] < $maxPosition) {
            $this->carouselPositions[$categoryId] += self::CARDS_PER_VIEW;
            // Update dot position
            $this->carouselDotPositions[$categoryId] = min(
                $maxDots,
                floor($this->carouselPositions[$categoryId] / self::CARDS_PER_VIEW)
            );
        } else {
            $this->carouselPositions[$categoryId] = 0;
            $this->carouselDotPositions[$categoryId] = 0;
        }
    }

    public function prevCarousel(int $categoryId)
    {
        if (!isset($this->carouselPositions[$categoryId])) {
            $this->carouselPositions[$categoryId] = 0;
            $this->carouselDotPositions[$categoryId] = 0;
        }

        $businessCount = $this->getBusinessCountForCategory($categoryId);
        $maxPosition = max(0, $businessCount - self::CARDS_PER_VIEW);
        $maxDots = max(0, ceil($businessCount / self::CARDS_PER_VIEW) - 1);

        if ($this->carouselPositions[$categoryId] > 0) {
            $this->carouselPositions[$categoryId] -= self::CARDS_PER_VIEW;
            // Update dot position
            $this->carouselDotPositions[$categoryId] = max(
                0,
                floor($this->carouselPositions[$categoryId] / self::CARDS_PER_VIEW)
            );
        } else {
            $this->carouselPositions[$categoryId] = $maxPosition;
            $this->carouselDotPositions[$categoryId] = $maxDots;
        }
    }

    public function gotoCarouselPosition(int $categoryId, int $position)
    {
        $this->carouselPositions[$categoryId] = $position * self::CARDS_PER_VIEW;
        $this->carouselDotPositions[$categoryId] = $position;
    }

    private function getBusinessCountForCategory(int $categoryId): int
    {
        return Business::where('category_id', $categoryId)
            ->when($this->search, function ($q) {
                $q->where(function($query) {
                    $query->where('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('business_desc', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('country', 'like', '%' . $this->search . '%')
                        ->orWhereHas('category', function ($catQuery) {
                            $catQuery->where('type', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->count();
    }

    private function getBusinessQueryForCategory(int $categoryId)
    {
        return Business::with(['vendor', 'packages', 'reviews', 'category', 'subcategory'])
            ->where('category_id', $categoryId)
            ->when($this->search, function ($q) {
                $q->where(function($query) {
                    $query->where('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('business_desc', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('country', 'like', '%' . $this->search . '%')
                        ->orWhereHas('category', function ($catQuery) {
                            $catQuery->where('type', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->withCount(['packages', 'reviews'])
            ->orderBy('rating', 'desc')
            ->orderBy('is_featured', 'desc');
    }

    // Helper method to get important business data
    private function getBusinessImportantData($business)
    {
        return [
            'rating' => $business->rating ?? 0,
            'reviews_count' => $business->reviews_count ?? 0,
            'packages_count' => $business->packages_count ?? 0,
            'location' => trim($business->city . ', ' . $business->country, ', '),
            'experience' => $business->vendor->years_of_experience ?? 'N/A',
            'capacity' => $business->capacity ?? 'N/A',
            'is_featured' => $business->is_featured ?? false,
            'description_short' => Str::limit($business->business_desc ?? '', 100),
            'member_type' => $business->member_type ?? 'Standard',
        ];
    }

    public function render()
    {
        $favouriteIds = Favourites::where('host_id', Auth::guard('host')->id())
            ->pluck('business_id')
            ->toArray();

        $categories = Category::orderBy('type')->get();

        if ($this->categoryFilter) {
            $query = $this->getBusinessQueryForCategory($this->categoryFilter);
            $totalBusinesses = $query->count();

            if ($totalBusinesses > 25) {
                $businesses = $query->paginate($this->perPage);
                $usePagination = true;
            } else {
                $businesses = $query->get();
                $usePagination = false;
            }

            return view('livewire.host.vendors.index', [
                'categories' => $categories,
                'businesses' => $businesses,
                'favouriteIds' => $favouriteIds,
                'viewMode' => 'grid',
                'usePagination' => $usePagination,
                'totalBusinesses' => $totalBusinesses,
                'getBusinessImportantData' => fn($business) => $this->getBusinessImportantData($business),
            ]);
        }

        $categoryData = [];
        foreach ($categories as $category) {
            $businesses = $this->getBusinessQueryForCategory($category->id)
                ->limit(12)
                ->get();

            if ($businesses->count() > 0) {
                $categoryData[] = [
                    'category' => $category,
                    'businesses' => $businesses,
                    'total_dots' => ceil($businesses->count() / self::CARDS_PER_VIEW),
                ];
            }
        }

        return view('livewire.host.vendors.index', [
            'categories' => $categories,
            'categoryData' => $categoryData,
            'favouriteIds' => $favouriteIds,
            'viewMode' => 'carousel',
            'maxCardsBeforeCarousel' => self::MAX_CARDS_BEFORE_CAROUSEL,
            'cardsPerView' => self::CARDS_PER_VIEW,
            'carouselStep' => self::CAROUSEL_STEP,
            'getBusinessImportantData' => fn($business) => $this->getBusinessImportantData($business),
        ]);
    }
}
