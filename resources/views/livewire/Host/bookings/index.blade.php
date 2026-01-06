<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bookings</h1>
            <p class="mt-1 text-gray-600">Manage your event bookings</p>
        </div>
        <flux:button href="{{ route('host.bookings.create') }}" wire:navigate variant="primary">
            <flux:icon.plus class="size-5" />
            New Booking
        </flux:button>
    </div>

    <flux:card>
        {{-- Filters --}}
        <div class="p-6 border-b space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by vendor name..."
                    icon="magnifying-glass"
                />

                <flux:select wire:model.live="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </flux:select>

                <flux:select wire:model.live="sortBy">
                    <option value="event_date">Sort by Event Date</option>
                    <option value="created_at">Sort by Created Date</option>
                    <option value="amount">Sort by Amount</option>
                </flux:select>
            </div>
        </div>

        {{-- Bookings Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking->business->company_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->vendor->full_name ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $booking->event_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $booking->package->name ?? 'Custom' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            ${{ number_format($booking->final_amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <flux:badge color="{{ match($booking->status) {
                                    'confirmed' => 'green',
                                    'pending' => 'yellow',
                                    'cancelled' => 'red',
                                    default => 'gray'
                                } }}">
                                {{ ucfirst($booking->status) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    href="{{ route('host.bookings.show', $booking) }}"
                                    wire:navigate
                                    variant="ghost"
                                    size="sm"
                                >
                                    View
                                </flux:button>
                                <flux:button
                                    href="{{ route('host.bookings.edit', $booking) }}"
                                    wire:navigate
                                    variant="ghost"
                                    size="sm"
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    wire:click="deleteBooking({{ $booking->id }})"
                                    wire:confirm="Are you sure you want to delete this booking?"
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No bookings found. Create your first booking to get started.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-6 border-t">
            {{ $bookings->links() }}
        </div>
    </flux:card>
</div>
