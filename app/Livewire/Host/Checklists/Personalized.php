<?php

namespace App\Livewire\Host\Checklists;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Host\HostPersonalizedChecklist;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Personalized Checklist')]
class Personalized extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $categoryFilter = 'all';
    public bool $showModal = false;

    // Checklist form fields
    public ?int $checklistId = null;
    public string $check_list_title = '';
    public string $check_list_category = '';
    public string $check_list_description = '';
    public string $check_list_due_date = '';
    public string $checklist_status = 'pending';
    public bool $is_custom = true;

    protected $queryString = ['search', 'statusFilter', 'categoryFilter'];

    protected function rules(): array
    {
        return [
            'check_list_title' => ['required', 'string', 'max:255'],
            'check_list_category' => ['required', 'string', 'max:100'],
            'check_list_description' => ['nullable', 'string'],
            'check_list_due_date' => ['required', 'date'],
            'checklist_status' => ['required', 'in:pending,in_progress,completed'],
        ];
    }

    public function openModal(?int $checklistId = null): void
    {
        $this->resetForm();

        if ($checklistId) {
            $checklist = HostPersonalizedChecklist::where('id', $checklistId)
                ->where('host_id', Auth::guard('host')->id())
                ->firstOrFail();

            $this->checklistId = $checklist->id;
            $this->check_list_title = $checklist->check_list_title;
            $this->check_list_category = $checklist->check_list_category;
            $this->check_list_description = $checklist->check_list_description ?? '';
            $this->check_list_due_date = $checklist->check_list_due_date->format('Y-m-d');
            $this->checklist_status = $checklist->checklist_status;
        } else {
            $this->check_list_due_date = now()->addDays(7)->format('Y-m-d');
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveChecklist(): void
    {
        $validated = $this->validate();

        HostPersonalizedChecklist::updateOrCreate(
            ['id' => $this->checklistId],
            [
                'host_id' => Auth::guard('host')->id(),
                'check_list_title' => $validated['check_list_title'],
                'check_list_category' => $validated['check_list_category'],
                'check_list_description' => $validated['check_list_description'],
                'check_list_due_date' => $validated['check_list_due_date'],
                'checklist_status' => $validated['checklist_status'],
                'is_custom' => true,
            ]
        );

        session()->flash('success', $this->checklistId ? 'Task updated successfully.' : 'Task created successfully.');
        $this->closeModal();
    }

    public function toggleStatus(int $checklistId): void
    {
        $checklist = HostPersonalizedChecklist::where('id', $checklistId)
            ->where('host_id', Auth::guard('host')->id())
            ->firstOrFail();

        $checklist->update([
            'checklist_status' => $checklist->checklist_status === 'completed' ? 'pending' : 'completed'
        ]);
    }

    public function deleteChecklist(int $checklistId): void
    {
        HostPersonalizedChecklist::where('id', $checklistId)
            ->where('host_id', Auth::guard('host')->id())
            ->delete();

        session()->flash('success', 'Task deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->checklistId = null;
        $this->check_list_title = '';
        $this->check_list_category = '';
        $this->check_list_description = '';
        $this->check_list_due_date = '';
        $this->checklist_status = 'pending';
        $this->resetValidation();
    }

    public function render()
    {
        $checklists = HostPersonalizedChecklist::where('host_id', Auth::guard('host')->id())
            ->when($this->search, function ($query) {
                $query->where('check_list_title', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('checklist_status', $this->statusFilter);
            })
            ->when($this->categoryFilter !== 'all', function ($query) {
                $query->where('check_list_category', $this->categoryFilter);
            })
            ->orderBy('check_list_due_date')
            ->paginate(15);

        $categories = HostPersonalizedChecklist::where('host_id', Auth::guard('host')->id())
            ->distinct()
            ->pluck('check_list_category');

        return view('livewire.host.checklists.personalized', [
            'checklists' => $checklists,
            'categories' => $categories,
        ]);
    }
}
