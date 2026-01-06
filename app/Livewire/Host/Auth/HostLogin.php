<?php

namespace App\Livewire\Host\Auth;

use App\Models\Host\Host;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HostLogin extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function login()
    {
        $host = Host::where('email', $this->email)->first();

        if (!$host) {
            $this->addError('email', 'No account found with this email.');
            return;
        }

        if ($host->account_deactivated) {
            $this->addError('email', 'Your account has been deactivated.');
            return;
        }

        if ($host->account_soft_deleted) {
            $this->addError('email', 'Your account has been deleted.');
            return;
        }

        if (!$host->is_verified) {
            session(['host_signup_id' => $host->id]);
            return redirect()->route('host.verify-otp');
        }

        if (!Auth::guard('host')->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            $this->addError('password', 'Invalid credentials.');
            return;
        }

        session()->regenerate();

        return redirect()->route('host.host-dashboard');
    }

    public function render()
    {
        return view('livewire.auth.host-login')
            ->layout('components.layouts.auth.simple');
    }
}
