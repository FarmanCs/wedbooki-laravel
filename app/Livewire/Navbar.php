<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Business;

class Navbar extends Component
{
    public $searchValue = '';
    public $selectedCategory = 'Venue';
    public $searchFocused = false;
    public $categoryOpen = false;
    public $mobileMenuOpen = false;
    public $showProfileMenu = false;
    public $mobileProfileOpen = false;
    public $searchResults = [];

    public $host;

    protected $searchCategories = ['Venue', 'Vendor'];

    public function mount()
    {
        $this->host = Auth::guard('host')->user();
    }

    public function updatedSearchValue()
    {
        if (strlen($this->searchValue) > 0) {
            // Adjust this query based on your Business model structure
            $this->searchResults = Business::where('company_name', 'like', '%' . $this->searchValue . '%')
                ->where('category', $this->selectedCategory)
                ->limit(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->categoryOpen = false;
        $this->updatedSearchValue();
    }

    public function toggleCategoryDropdown()
    {
        $this->categoryOpen = !$this->categoryOpen;
        if ($this->categoryOpen) {
            $this->searchFocused = false;
        }
    }

    public function focusSearch()
    {
        $this->searchFocused = true;
        if ($this->categoryOpen) {
            $this->categoryOpen = false;
        }
    }

    public function toggleMobileMenu()
    {
        $this->mobileMenuOpen = !$this->mobileMenuOpen;
    }

    public function toggleProfileMenu()
    {
        $this->showProfileMenu = !$this->showProfileMenu;
    }

    public function toggleMobileProfile()
    {
        $this->mobileProfileOpen = !$this->mobileProfileOpen;
    }

    public function logout()
    {
        Auth::guard('host')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('host.host-login');
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
