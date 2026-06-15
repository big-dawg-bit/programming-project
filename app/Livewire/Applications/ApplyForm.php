<?php

namespace App\Livewire\Applications;

use App\Models\Company;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class ApplyForm extends Component
{
    public ?int $company_id = null;

    // Nieuw bedrijf toevoegen i.p.v. een bestaand kiezen.
    public bool $newCompany = false;
    public string $company_name = '';
    public string $company_address = '';
    public string $company_vat_number = '';
    public string $company_contact_email = '';
    public string $company_contact_phone = '';

    public string $position_title = '';
    public string $description = '';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public string $proposed_mentor_name = '';

    public function submit(): void
    {
        $rules = [
            'position_title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'proposed_mentor_name' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->newCompany) {
            $rules += [
                'company_name' => ['required', 'string', 'max:255'],
                'company_address' => ['nullable', 'string', 'max:255'],
                'company_vat_number' => ['nullable', 'string', 'max:255'],
                'company_contact_email' => ['nullable', 'email', 'max:255'],
                'company_contact_phone' => ['nullable', 'string', 'max:255'],
            ];
        } else {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        }

        $this->validate($rules);

        // Nieuw bedrijf aanmaken en koppelen.
        if ($this->newCompany) {
            $company = Company::create([
                'name' => $this->company_name,
                'address' => $this->company_address ?: null,
                'vat_number' => $this->company_vat_number ?: null,
                'contact_email' => $this->company_contact_email ?: null,
                'contact_phone' => $this->company_contact_phone ?: null,
            ]);

            $this->company_id = $company->id;
        }

        auth()->user()->student->applications()->create([
            'company_id' => $this->company_id,
            'position_title' => $this->position_title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'proposed_mentor_name' => $this->proposed_mentor_name ?: null,
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
