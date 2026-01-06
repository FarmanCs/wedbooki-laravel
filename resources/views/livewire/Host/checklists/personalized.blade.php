<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Personalized Checklist</h1>
            <p class="mt-1 text-gray-600">Manage your event tasks and to-dos</p>
        </div>
        <flux:button wire:click="openModal" variant="primary">
            <flux:icon.plus class="size-5"/>
            Add Task
        </flux:button>
    </div>

    <flux:card>
        {{-- Filters --}}
        <div class="p-6 border-b space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search tasks..."
                    icon="magnifying-glass"
                />

                <flux:select wire:model.live="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </flux:select>

                <flux:select wire:model.live="categoryFilter">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- Checklist Items --}}
        <div class="divide-y">
            @forelse($checklists as $item)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 pt-1">
                            <flux:checkbox
                                wire:click="toggleStatus({{ $item->id }})"
                                :checked="$item->checklist_status === 'completed'"
                            />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 {{ $item->checklist_status === 'completed' ? 'line-through' : '' }}">
                                        {{ $item->check_list_title }}
                                    </h3>

                                    @if($item->check_list_description)
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $item->check_list_description }}
                                        </p>
                                    @endif

                                    <div class="mt-2 flex flex-wrap items-center gap-3">
                                        <flux:badge size="sm">{{ ucfirst($item->check_list_category) }}</flux:badge>

                                        <div class="flex items-center gap-1 text-sm text-gray-500">
                                            <flux:icon.calendar class="size-4"/>
                                            {{ $item->check_list_due_date->format('M d, Y') }}
                                        </div>

                                        @if($item->check_list_due_date->isPast() && $item->checklist_status !== 'completed')
                                            <flux:badge color="red" size="sm">Overdue</flux:badge>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <flux:button
                                        wire:click="openModal({{ $item->id }})"
                                        variant="ghost"
                                        size="sm"
                                    >
                                        <flux:icon.pencil class="size-4"/>
                                    </flux:button>
                                    <flux:button
                                        wire:click="deleteChecklist({{ $item->id }})"
                                        wire:confirm="Are you sure you want to delete this task?"
                                        variant="ghost"
                                        size="sm"
                                        color="red"
                                    >
                                        <flux:icon.trash class="size-4"/>
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <flux:icon.clipboard-list class="size-12 mx-auto text-gray-400 mb-4"/>
                    <p>No tasks found. Create your first task to get started.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($checklists->hasPages())
            <div class="p-6 border-t">
                {{ $checklists->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Task Modal --}}
    @if($showModal)
        <flux:modal wire:model="showModal" class="max-w-2xl">
            <flux:modal.content>
                <flux:heading size="lg">{{ $checklistId ? 'Edit Task' : 'Add New Task' }}</flux:heading>

                <form wire:submit="saveChecklist" class="space-y-4 mt-6">
                    <flux:input
                        wire:model="check_list_title"
                        label="Task Title"
                        placeholder="Enter task title"
                        required
                    />

                    <flux:input
                        wire:model="check_list_category"
                        label="Category"
                        placeholder="e.g., Venue, Catering, Decorations"
                        required
                    />

                    <flux:textarea
                        wire:model="check_list_description"
                        label="Description"
                        placeholder="Add any additional details..."
                        rows="3"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input
                            wire:model="check_list_due_date"
                            type="date"
                            label="Due Date"
                            required
                        />

                        <flux:select wire:model="checklist_status" label="Status">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </flux:select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button type="button" wire:click="closeModal" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $checklistId ? 'Update' : 'Create' }} Task
                        </flux:button>
                    </div>
                </form>
            </flux:modal.content>
        </flux:modal>
    @endif
</div>
