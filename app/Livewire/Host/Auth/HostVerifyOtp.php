<?php

namespace App\Livewire\Host\Auth;

use App\Models\Host\Host;
use App\Src\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;

class HostVerifyOtp extends Component
{
    public $otp = '';
    public Host $host;
    public int $secondsLeft = 0;
    public string $errorMessage = '';

    protected $rules = [
        'otp' => 'required|digits:6',
    ];

    protected $messages = [
        'otp.required' => 'Please enter the verification code.',
        'otp.digits' => 'The verification code must be exactly 6 digits.',
    ];

    public function mount()
    {
        $hostId = session('host_signup_id');
        abort_unless($hostId, 403);

        $this->host = Host::findOrFail($hostId);

        if ($this->host->is_verified) {
            return redirect()->route('host.host-dashboard');
        }

        $this->updateCountdown();
    }

    public function updatedOtp()
    {
        $this->errorMessage = '';
        $this->resetErrorBag();
        $this->resetValidation('otp');
    }

    #[Computed]
    public function otpIsValid(): bool
    {
        return preg_match('/^\d{6}$/', $this->otp) === 1;
    }

    public function verifyOtp()
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        if (!$this->otpIsValid) {
            $this->errorMessage = 'Please enter a valid 6-digit OTP.';
            return;
        }

        if (!$this->host->otp || now()->isAfter($this->host->otp_expires_at)) {
            $this->errorMessage = 'Verification code has expired. Please request a new one.';
            return;
        }

        if ((int)$this->otp !== (int)$this->host->otp) {
            $this->errorMessage = 'Invalid verification code. Please try again.';
            return;
        }

        $this->host->update([
            'is_verified' => true,
            'status' => 'approved',
            'otp' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ]);

        Auth::guard('host')->login($this->host);
//        session()->forget('host_signup_id');
        session()->flash('success', 'Email verified successfully! Welcome to WedBooki.');

        return redirect()->route('host.host-dashboard');
    }

    public function resendOtp()
    {
        if ($this->secondsLeft > 0) {
            return; // prevent resend before timer ends
        }

        $otp = OtpService::generateOtp();
        $this->host->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addSeconds(30), // 30s validity
            'otp_attempts' => 0,
        ]);

        OtpService::sendOtp($this->host->email, $otp, $this->host->full_name);

        $this->secondsLeft = 30;
        $this->otp = '';
        $this->errorMessage = '';
        session()->flash('success', 'A new verification code has been sent to your email.');
    }

    public function updateCountdown()
    {
        $this->secondsLeft = max(
            0,
            now()->diffInSeconds($this->host->otp_expires_at ?? now(), false)
        );
    }

    public function render()
    {
        $this->updateCountdown();

        return view('livewire.auth.host-verify-otp')
            ->layout('components.layouts.auth.simple', [
                'title' => 'Verify Email',
            ]);
    }
}
