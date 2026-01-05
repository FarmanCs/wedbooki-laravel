<?php

namespace App\Livewire\Host\Auth;

use App\Models\Host\Host;
use App\Src\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class HostSignup extends Component
{
    public $full_name = '';
    public $email = '';
    public $country_code = '+92';
    public $phone_no = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'full_name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:hosts,email',
        'country_code' => 'required|string',
        'phone_no' => 'required|numeric|digits_between:8,15',
        'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/',
    ];

    protected $messages = [
        'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function signup()
    {
        $this->validate();

        try {
            // Generate OTP
            $otp = OtpService::generateOtp();

            // Create host with unverified status
            $host = Host::create([
                'full_name' => $this->full_name,
                'email' => $this->email,
                'country_code' => $this->country_code,
                'phone_no' => $this->phone_no,
                'password' => Hash::make($this->password),
                'otp' => $otp,
                'otp_expires_at'  => now()->addSeconds(30),
                'otp_attempts'    => 0,
                'is_verified' => false,
                'signup_method' => 'email',
                'status' => 'pending',
            ]);

            // Store host ID in session for OTP verification
            session(['host_signup_id' => $host->id]);
            session(['otp_last_sent' => now()]);

            // Try to send OTP email (non-blocking)
            try {
                OtpService::sendOtp($this->email, $otp, $this->full_name);
                session()->flash('success', 'Account created! Please check your email for the OTP.');
            } catch (\Exception $emailError) {
                \Log::error('Email Sending Error: ' . $emailError->getMessage());
                session()->flash('success', 'Account created! Your OTP is: ' . $otp . ' (Email sending failed, use this code)');
            }

            // Redirect to OTP verification
            return redirect()->route('host.verify-otp');

        } catch (\Exception $e) {
            \Log::error('Host Signup Error: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.host-signup')->layout('components.layouts.auth.split');
    }
}
