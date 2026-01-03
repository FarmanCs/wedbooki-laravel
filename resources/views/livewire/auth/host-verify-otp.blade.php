<div class="flex flex-col space-y-6">

    <!-- Header -->
    <div class="text-center space-y-2">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-600 text-white">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h2 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">
            Verify your email
        </h2>

        <p class="text-sm text-neutral-600 dark:text-neutral-400">
            We’ve sent a 6-digit code to<br>
            <span class="font-medium text-indigo-600 dark:text-indigo-400">
                {{ $host->email }}
            </span>
        </p>
    </div>

    <!-- Success -->
    @if (session()->has('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700
                    dark:border-green-800 dark:bg-green-950 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error -->
    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700
                    dark:border-red-800 dark:bg-red-950 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- OTP Form -->
    <form wire:submit.prevent="verifyOtp" class="space-y-4">

        <div class="space-y-1 text-center">
            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                Verification code
            </label>

            <input
                type="text"
                wire:model.lazy="otp"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="000000"
                autofocus
                class="mx-auto w-full rounded-md border border-neutral-300 bg-white px-3 py-3
                       text-center text-2xl font-semibold tracking-[0.4em]
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                       dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100"
            >

            @error('otp')
            <p class="text-xs text-red-600 dark:text-red-400">
                {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex h-10 w-full items-center justify-center rounded-md
                   bg-indigo-600 px-4 text-sm font-medium text-white
                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500
                   disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="verifyOtp">Verify account</span>
            <span wire:loading wire:target="verifyOtp">Verifying…</span>
        </button>
    </form>

    <!-- Resend -->
    <div class="text-center text-sm text-neutral-600 dark:text-neutral-400">
        @if ($canResend)
            <button
                wire:click="resendOtp"
                wire:loading.attr="disabled"
                class="font-medium text-indigo-600 hover:underline dark:text-indigo-400"
            >
                Resend code
            </button>
        @else
            Resend in
            <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                {{ $resendCooldown }}
            </span>s
        @endif
    </div>

</div>
