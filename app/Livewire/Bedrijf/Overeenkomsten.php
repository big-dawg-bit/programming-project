<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use App\Models\StageAgreement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Overeenkomsten')]
class Overeenkomsten extends Component
{
    public function sign(int $agreementId): void
    {
        $company = Company::where('user_id', Auth::id())->first();

        if (! $company) {
            return;
        }

        $applicationIds = $company->applications()->pluck('id');

        $agreement = StageAgreement::whereIn('application_id', $applicationIds)->find($agreementId);

        if (! $agreement) {
            return;
        }

        $agreement->update([
            'company_signature' => $company->name,
            'company_signed_at' => now(),
        ]);

        session()->flash('bedrijf-status', 'Overeenkomst getekend.');
    }

    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $applications = $company
            ? $company->applications()
                ->whereHas('agreement')
                ->with(['student.user', 'agreement'])
                ->get()
            : collect();

        return view('livewire.bedrijf.overeenkomsten', [
            'company' => $company,
            'applications' => $applications,
        ]);
    }
}
