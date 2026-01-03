<div class="flex flex-col gap-6">
    <x-auth-header
        title="Create an account"
        description="Enter your details below to create your account"
    />

    <form wire:submit.prevent="signup" class="flex flex-col gap-6">

        <flux:input
            label="Full Name"
            placeholder="Your full name"
            wire:model.defer="full_name"
            required
        />

        <flux:input
            type="email"
            label="Email Address"
            placeholder="email@example.com"
            wire:model.defer="email"
            required
        />

        <div class="flex gap-3">
            <div class="w-20 shrink-0">
                <flux:input
                    label="Code"
                    wire:model.defer="country_code"
                />
            </div>

            <div class="flex-1">
                <flux:input
                    label="Phone Number"
                    placeholder="3001234567"
                    wire:model.defer="phone_no"
                    required
                />
            </div>
        </div>


        <flux:input
            type="password"
            label="Password"
            wire:model.defer="password"
            viewable
            required
        />

        <flux:input
            type="password"
            label="Confirm Password"
            wire:model.defer="password_confirmation"
            viewable
            required
        />

        <flux:button type="submit" variant="primary" class="w-full">
            Create Account
        </flux:button>
    </form>

    <div class="text-center text-sm text-zinc-600 dark:text-white">
        Already have an account?
        <flux:link href="{{ route('host.host-login') }}" wire:navigate>
            <span class="text-blue-500">Log in</span>
        </flux:link>
    </div>
</div>
