<?php

namespace App\Livewire\Auth;

use App\Models\Host\Host;
use App\Src\Services\OtpService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HostVerifyOtp extends Component
{
    public $otp = '';
    public $host;
    public $resendCooldown = 0;
    public $canResend = false;

    protected $rules = [
        'otp' => 'required|numeric|digits:6',
    ];

    public function mount()
    {
        $hostId = session('host_signup_id');

        if (!$hostId) {
            session()->flash('error', 'Session expired. Please sign up again.');
            $this->redirect(route('host.signup'), navigate: true);
            return;
        }

        $this->host = Host::find($hostId);

        if (!$this->host) {
            session()->flash('error', 'Invalid session. Please sign up again.');
            $this->redirect(route('host.signup'), navigate: true);
            return;
        }

        if ($this->host->is_verified) {
            $this->redirect(route('host.host-dashboard'), navigate: true);
            return;
        }

        $this->calculateResendCooldown();
    }

    public function verifyOtp()
    {
        $this->validate();

        if ((int) $this->host->otp !== (int) $this->otp) {
            $this->addError('otp', 'Invalid OTP code. Please try again.');
            return;
        }

        $this->host->update([
            'is_verified'       => true,
            'email_verified_at' => now(),
            'status'            => 'approved',
            'otp'               => null,
        ]);

        Auth::guard('host')->login($this->host);

        session()->forget(['host_signup_id', 'otp_last_sent']);
        session()->flash('success', 'Email verified successfully! Welcome to WedBooki.');

        $this->redirect(route('host.host-dashboard'), navigate: true);
    }

    public function resendOtp()
    {
        if (!$this->canResend) {
            return;
        }

        $newOtp = OtpService::generateOtp();

        $this->host->update(['otp' => $newOtp]);
        session(['otp_last_sent' => now()]);

        try {
            OtpService::sendOtp(
                $this->host->email,
                $newOtp,
                $this->host->full_name
            );
            session()->flash('success', 'New OTP sent to your email!');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            session()->flash('success', "OTP: {$newOtp} (email failed)");
        }

        $this->calculateResendCooldown();
    }

    public function calculateResendCooldown()
    {
        $cooldownSeconds = 60;

        $lastSent = session('otp_last_sent', now()->subSeconds(61));
        $elapsed  = now()->diffInSeconds($lastSent);

        $this->resendCooldown = max(0, $cooldownSeconds - $elapsed);
        $this->canResend = $this->resendCooldown === 0;
    }

    public function render()
    {
        $this->calculateResendCooldown();

        return view('livewire.auth.host-verify-otp')
            ->layout('components.layouts.auth.simple', ['title' => 'Verify Email']);
    }
}
