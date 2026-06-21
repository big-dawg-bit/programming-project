<?php

namespace App\Livewire\Applications;

use App\Models\Company;
use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class ApplyForm extends Component
{
    /** Gevuld bij bewerken/opnieuw indienen; null bij een nieuwe aanvraag. */
    public ?StageApplication $application = null;

    public ?int $company_id = null;

    public string $position_title = '';

    public string $description = '';

    public ?string $start_date = null;

    public ?string $end_date = null;

    public string $proposed_mentor_name = '';

    // --- Nieuw bedrijf toevoegen (inline) ---
    public bool $addingCompany = false;

    public string $new_company_name = '';

    public string $new_company_address = '';

    public string $new_company_vat = '';

    public string $new_company_email = '';

    public function mount(?StageApplication $application = null): void
    {
        // Geen route-parameter (= aanmaken) -> Livewire geeft null of een leeg model.
        if (! $application?->exists) {
            return;
        }

        // Scoping: enkel de eigen aanvraag, en enkel na "aanpassingen vereist".
        abort_unless($application->student_id === auth()->user()->student?->id, 403);
        abort_unless($application->status === 'changes_requested', 403);

        $this->application = $application;
        $this->company_id = $application->company_id;
        $this->position_title = $application->position_title ?? '';
        $this->description = $application->description ?? '';
        $this->start_date = optional($application->start_date)->format('Y-m-d');
        $this->end_date = optional($application->end_date)->format('Y-m-d');
        $this->proposed_mentor_name = $application->proposed_mentor_name ?? '';
    }

    /** Maakt een nieuw bedrijf aan en koppelt het meteen aan de aanvraag-in-opbouw. */
    public function addCompany(): void
    {
        // Btw normaliseren: spaties/punten/streepjes weg en hoofdletters, zodat
        // "be 0123.456.789" ook als "BE0123456789" wordt gevalideerd.
        $this->new_company_vat = strtoupper(str_replace([' ', '.', '-'], '', $this->new_company_vat));

        $data = $this->validate([
            'new_company_name' => ['required', 'string', 'max:255'],
            'new_company_address' => ['nullable', 'string', 'max:255'],
            'new_company_vat' => ['required', 'regex:/^BE[0-9]{10}$/'],
            'new_company_email' => ['nullable', 'email', 'max:255'],
        ], [
            'new_company_vat.required' => 'Vul het btw-nummer in.',
            'new_company_vat.regex' => 'Het btw-nummer moet beginnen met BE, gevolgd door 10 cijfers (bv. BE0123456789).',
        ]);

        $company = Company::create([
            'name' => $data['new_company_name'],
            'address' => $data['new_company_address'] ?: null,
            'vat_number' => $data['new_company_vat'],
            'contact_email' => $data['new_company_email'] ?: null,
        ]);

        $this->company_id = $company->id;

        $this->reset([
            'addingCompany',
            'new_company_name',
            'new_company_address',
            'new_company_vat',
            'new_company_email',
        ]);
    }

    public function submit()
    {
        $data = $this->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'position_title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'proposed_mentor_name' => ['nullable', 'string', 'max:255'],
        ]);

        // --- Opnieuw indienen na "aanpassingen vereist" ---
        if ($this->application) {
            $this->application->update([
                ...$data,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Terug naar "Mijn stages" met een bevestigingsmelding.
            session()->flash('stage-melding', 'Je stageaanvraag is opnieuw ingediend.');

            return $this->redirectRoute('student.stage', navigate: true);
        }

        // --- Nieuwe aanvraag ---
        auth()->user()->student->applications()->create([
            ...$data,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Terug naar "Mijn stages" met een bevestigingsmelding.
        session()->flash('stage-melding', 'Je stageaanvraag is ingediend.');

        return $this->redirectRoute('student.stage', navigate: true);
    }

    public function render()
    {
        return view('livewire.applications.apply-form', [
            'companies' => Company::orderBy('name')->get(),
            // Laatste "aanpassingen vereist"-feedback, getoond bij het bewerken.
            'feedback' => $this->application?->reviews()
                ->where('decision', 'changes_requested')
                ->latest('reviewed_at')
                ->value('feedback'),
        ]);
    }
}
