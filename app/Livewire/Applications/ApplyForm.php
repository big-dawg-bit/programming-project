<?php

namespace App\Livewire\Applications;

use App\Models\Company;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class ApplyForm extends Component
{
    public ?int $company_id = null;
    public string $position_title = '';
    public string $description = '';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public string $proposed_mentor_name = '';

    public function submit(): void
    {
        $data = $this->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'position_title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'proposed_mentor_name' => ['nullable', 'string', 'max:255'],
        ]);

        auth()->user()->student->applications()->create([
            ...$data,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        session()->flash('status', 'Aanvraag ingediend.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.applications.apply-form', [
            'companies' => Company::orderBy('name')->get(),
        ]);
    }
}
