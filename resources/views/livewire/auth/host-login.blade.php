<div class="flex flex-col gap-6">

    <x-auth-header
        title="Log in to your account"
        description="Enter your email and password below to log in"
    />

    <form wire:submit.prevent="login" class="flex flex-col gap-6">

        {{-- Email --}}
        <div wire:ignore x-data>
            <flux:input
                label="Email address"
                type="email"
                placeholder="email@example.com"
                x-model="$wire.email"
            />
        </div>
        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Password --}}
        <div wire:ignore x-data>
            <flux:input
                label="Password"
                type="password"
                viewable
                x-model="$wire.password"
            />
        </div>
        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Remember Me --}}
        <div wire:ignore x-data>
            <flux:checkbox
                label="Remember me"
                x-model="$wire.remember"
            />
        </div>

        {{-- Submit --}}
        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Log in</span>
            <span wire:loading>Logging in...</span>
        </flux:button>

    </form>

    <div class="text-center text-sm text-zinc-600">
        Don’t have an account?
        <flux:link href="{{ route('host.host-signup') }}">
            Sign up
        </flux:link>
    </div>

</div>
