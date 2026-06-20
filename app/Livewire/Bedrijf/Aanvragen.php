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

        // Enkel aanvragen die nog op een beslissing van het bedrijf wachten.
        $application = $company->applications()->where('company_status', 'pending')->find($id);
        if (! $application) {
            return;
        }

        $data = ['company_status' => $status];
        if ($status === 'refused') {
            $data['status'] = 'rejected';   // student ziet de afwijzing
        }
        $application->update($data);

        session()->flash('bedrijf-status', $status === 'accepted' ? 'Aanvraag geaccepteerd.' : 'Aanvraag geweigerd.');
    }

    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $applications = $company
            ? $company->applications()->where('company_status', 'pending')->with('student.user')->latest()->get()
            : collect();

        return view('livewire.bedrijf.aanvragen', [
            'company' => $company,
            'applications' => $applications,
        ]);
    }
}
