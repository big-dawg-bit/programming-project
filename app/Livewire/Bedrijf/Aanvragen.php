<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Aanvragen')]
class Aanvragen extends Component
{
    public function accept(int $id): void
    {
        $this->setStatus($id, 'accepted');
    }

    public function refuse(int $id): void
    {
        $this->setStatus($id, 'refused');
    }

    protected function setStatus(int $id, string $status): void
    {
        $company = Company::where('user_id', Auth::id())->first();

        if (! $company) {
            return;
        }

        $application = $company->applications()->find($id);

        if (! $application) {
            return;
        }

        $application->update(['company_status' => $status]);

        session()->flash('bedrijf-status', $status === 'accepted' ? 'Aanvraag geaccepteerd.' : 'Aanvraag geweigerd.');
    }

    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $applications = $company
            ? $company->applications()->with('student.user')->latest()->get()
            : collect();

        return view('livewire.bedrijf.aanvragen', [
            'company' => $company,
            'applications' => $applications,
        ]);
    }
}
