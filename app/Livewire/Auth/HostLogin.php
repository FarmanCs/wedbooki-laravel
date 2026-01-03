<?php

namespace App\Livewire\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Host\Host;
use Livewire\Component;

class HostLogin extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function login()
    {
        $this->validate();

        try {
            // Find host by email
            $host = Host::where('email', $this->email)->first();

            if (!$host) {
                $this->addError('email', 'No account found with this email.');
                return;
            }

            // Check if account is deactivated or deleted
            if ($host->account_deactivated) {
                $this->addError('email', 'Your account has been deactivated. Please contact support.');
                return;
            }

            if ($host->account_soft_deleted) {
                $this->addError('email', 'Your account has been deleted. Please contact support to restore.');
                return;
            }

            // Verify password
            if (!Hash::check($this->password, $host->password)) {
                $this->addError('password', 'Invalid password.');
                return;
            }

            // Check if email is verified
            if (!$host->is_verified) {
                session(['host_signup_id' => $host->id]);
                session()->flash('error', 'Please verify your email first.');
                return redirect()->route('host.verify-otp');
            }

            // Create authentication token
            $token = $host->createToken('host-auth')->plainTextToken;

            // Store token in session
            session(['host_token' => $token]);

            // Login the host
            Auth::guard('host')->login($host, $this->remember);

            session()->flash('success', 'Welcome back, ' . $host->full_name . '!');

            return redirect()->route('host.host-login');

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred. Please try again.');
            \Log::error('Host Login Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.host-login')
            ->layout('components.layouts.auth.simple');
    }
}
