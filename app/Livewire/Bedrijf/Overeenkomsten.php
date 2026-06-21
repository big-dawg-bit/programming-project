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
    public ?int $signingAgreementId = null;

    public function openSign(int $agreementId): void
    {
        $this->signingAgreementId = $agreementId;
    }

    public function closeSign(): void
    {
        $this->signingAgreementId = null;
    }

    public function signCompany(string $signature): void
    {
        $company = Company::where('user_id', Auth::id())->first();
        if (! $company) {
            return;
        }

        $applicationIds = $company->applications()->pluck('id');
        $agreement = StageAgreement::whereIn('application_id', $applicationIds)
            ->with('application.stage')
            ->find($this->signingAgreementId);
        if (! $agreement) {
            return;
        }

        validator(['signature' => $signature], [
            'signature' => 'required|string|starts_with:data:image/png;base64,|max:500000',
        ])->validate();

        // Ondertekenen kan pas zodra de stage een begeleidende docent én een
        // stagementor heeft (dezelfde regel als aan de studentzijde).
        $stage = $agreement->application?->stage;
        if (! $stage || ! $stage->mentor_id || ! $stage->docent_id) {
            session()->flash('bedrijf-status', 'Je kan pas tekenen zodra de begeleidende docent en de stagementor zijn toegewezen.');
            $this->closeSign();

            return;
        }

        // Een al getekende of bevestigde overeenkomst wordt niet opnieuw getekend.
        if ($agreement->status !== 'bevestigd' && ! $agreement->company_signature) {
            $agreement->fill([
                'company_signature' => $signature,
                'company_signed_at' => now(),
            ])->save();

            $agreement->syncSignatureStatus();

            session()->flash('bedrijf-status', 'Overeenkomst getekend.');
        }

        $this->closeSign();
    }

    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $applications = $company
            ? $company->applications()->whereHas('agreement')->with(['student.user', 'agreement'])->get()
            : collect();

        $signingApplication = null;
        if ($company && $this->signingAgreementId) {
            $agreement = StageAgreement::whereIn('application_id', $company->applications()->pluck('id'))
                ->find($this->signingAgreementId);

            if ($agreement) {
                $signingApplication = $company->applications()
                    ->whereKey($agreement->application_id)
                    ->with(['student.user', 'company', 'stage.mentor.user', 'stage.docent.user', 'agreement'])
                    ->first();
            }
        }

        return view('livewire.bedrijf.overeenkomsten', [
            'company' => $company,
            'applications' => $applications,
            'signingApplication' => $signingApplication,
        ]);
    }
}
