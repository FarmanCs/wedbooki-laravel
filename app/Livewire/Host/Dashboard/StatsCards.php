<?php

namespace App\Livewire\Host\Dashboard;

use Livewire\Component;

class StatsCards extends Component
{
    public array $stats = [];

    public function render()
    {
        return view('livewire.host.dashboard.stats-cards');
    }
}
