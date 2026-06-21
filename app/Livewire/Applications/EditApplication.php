<?php

namespace App\Livewire\Applications;

use App\Models\Company;
use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class EditApplication extends Component
{
    public StageApplication $application;

    public int $company_id = 0;

    public string $position_title = '';

    public string $description = '';

    public string $start_date = '';

    public string $end_date = '';

    public string $proposed_mentor_name = '';

    public function mount(StageApplication $application): void
    {
        // enkel de eigenaar, en enkel bij 'aanpassingen vereist'
        abort_unless(
            $application->student_id === auth()->user()->student?->id
            && $application->status === 'changes_requested',
            403
        );

        $this->application = $application;
        $this->company_id = $application->company_id;
        $this->position_title = $application->position_title;
        $this->description = $application->description;
        $this->start_date = $application->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $application->end_date?->format('Y-m-d') ?? '';
        $this->proposed_mentor_name = $application->proposed_mentor_name ?? '';
    }

    public function resubmit(): void
    {
        $data = $this->validate([
            'company_id' => 'required|exists:companies,id',
            'position_title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'proposed_mentor_name' => 'nullable|string|max:255',
        ]);

        $this->application->update([
            ...$data,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        session()->flash('status', 'Je aanvraag is opnieuw ingediend.');

        $this->redirectRoute('student.dashboard');
    }

    public function render()
    {
        return view('livewire.applications.edit-application', [
            'companies' => Company::orderBy('name')->get(),
            'feedback' => $this->application->reviews()
                ->where('decision', 'changes_requested')
                ->latest('reviewed_at')
                ->first()?->feedback,
        ]);
    }
}
