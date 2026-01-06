<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Guests</h1>
            <p class="mt-1 text-gray-600">Manage your event guests</p>
        </div>
        <div class="flex gap-3">
            <flux:button href="{{ route('host.guests.groups') }}" wire:navigate variant="ghost">
                <flux:icon.user-group class="size-5"/>
                Manage Groups
            </flux:button>
            <flux:button wire:click="openModal" variant="primary">
                <flux:icon.plus class="size-5"/>
                Add Guest
            </flux:button>
        </div>
    </div>

    <flux:card>
        {{-- Filters --}}
        <div class="p-6 border-b space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search guests..."
                    icon="magnifying-glass"
                />

                <flux:select wire:model.live="groupFilter">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- Guests Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groups</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($guests as $guest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $guest->full_name }}</p>
                            @if($guest->address)
                                <p class="text-sm text-gray-500">{{ $guest->city }}, {{ $guest->state }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($guest->email)
                                <p class="text-sm text-gray-900">{{ $guest->email }}</p>
                            @endif
                            @if($guest->phone_no)
                                <p class="text-sm text-gray-500">{{ $guest->phone_no }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($guest->groups as $group)
                                    <flux:badge size="sm">{{ $group->group_name }}</flux:badge>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <flux:badge color="{{ $guest->is_joining ? 'green' : 'gray' }}">
                                {{ $guest->is_joining ? 'Attending' : 'Not Attending' }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    wire:click="openModal({{ $guest->id }})"
                                    variant="ghost"
                                    size="sm"
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    wire:click="deleteGuest({{ $guest->id }})"
                                    wire:confirm="Are you sure you want to delete this guest?"
                                    variant="ghost"
                                    size="sm"
                                    color="red"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No guests found. Add your first guest to get started.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-6 border-t">
            {{ $guests->links() }}
        </div>
    </flux:card>

    {{-- Guest Modal --}}
    @if($showModal)
        <flux:modal wire:model="showModal" class="max-w-2xl">
            <flux:modal.content>
                <flux:heading size="lg">{{ $guestId ? 'Edit Guest' : 'Add New Guest' }}</flux:heading>

                <form wire:submit="saveGuest" class="space-y-4 mt-6">
                    <flux:input
                        wire:model="full_name"
                        label="Full Name"
                        required
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input
                            wire:model="email"
                            type="email"
                            label="Email"
                        />
                        <flux:input
                            wire:model="phone_no"
                            label="Phone"
                        />
                    </div>

                    <flux:input
                        wire:model="mobile_no"
                        label="Mobile"
                    />

                    <flux:textarea
                        wire:model="address"
                        label="Address"
                        rows="2"
                    />

                    <div class="grid grid-cols-3 gap-4">
                        <flux:input
                            wire:model="city"
                            label="City"
                        />
                        <flux:input
                            wire:model="state"
                            label="State"
                        />
                        <flux:input
                            wire:model="zipcode"
                            label="Zip Code"
                        />
                    </div>

                    <flux:checkbox
                        wire:model="is_joining"
                        label="Guest is attending"
                    />

                    <div>
                        <flux:label>Guest Groups</flux:label>
                        <div class="mt-2 space-y-2">
                            @foreach($groups as $group)
                                <flux:checkbox
                                    wire:model="selectedGroups"
                                    value="{{ $group->id }}"
                                    label="{{ $group->group_name }}"
                                />
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button type="button" wire:click="closeModal" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $guestId ? 'Update' : 'Create' }} Guest
                        </flux:button>
                    </div>
                </form>
            </flux:modal.content>
        </flux:modal>
    @endif
</div>
