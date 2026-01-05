<div class="flex flex-col gap-6">

    <x-auth-header
        title="Verify your email"
        description="Enter the 6-digit code we sent to your email"
    />

    <p class="text-center text-sm text-zinc-600">
        Code sent to
        <span class="font-medium text-indigo-600">{{ $host->email }}</span>
    </p>

    {{-- Success or Error message --}}
    @if (session()->has('success'))
        <div class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @elseif ($errorMessage)
        <div class="rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit.prevent="verifyOtp" class="flex flex-col gap-6">
        <div>
            <flux:input
                label="Verification code"
                type="text"
                inputmode="numeric"
                maxlength="6"
                placeholder="000000"
                autocomplete="one-time-code"
                wire:model.live="otp"
                input-class="text-center tracking-widest text-lg"
            />
        </div>

        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
            :disabled="!$this->otpIsValid"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove wire:target="verifyOtp">Verify account</span>
            <span wire:loading wire:target="verifyOtp">Verifying...</span>
        </flux:button>
    </form>

    <div class="text-center text-sm text-zinc-600" wire:poll.1s>
        @if ($secondsLeft > 0)
            OTP expires in
            <span class="font-medium text-indigo-600">{{ $secondsLeft }}</span>s
        @else
            <flux:link href="#" wire:click.prevent="resendOtp">
                Resend code
            </flux:link>
        @endif
    </div>
</div>
