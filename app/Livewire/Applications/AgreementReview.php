<?php

namespace App\Livewire\Applications;

use App\Models\StageAgreement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Stageovereenkomsten')]
class AgreementReview extends Component
{
    /** De stagecommissie bevestigt de ondertekening van een ingediende overeenkomst. */
    public function confirm(int $id): void
    {
        $agreement = StageAgreement::where('status', 'ingediend')->findOrFail($id);

        $agreement->update(['status' => 'bevestigd']);

        session()->flash('success', 'Overeenkomst bevestigd.');
    }

    public function render()
    {
        return view('livewire.applications.agreement-review', [
            'agreements' => StageAgreement::with(['application.student.user', 'application.company', 'file'])
                ->where('status', 'ingediend')
                ->latest('uploaded_at')
                ->get(),
        ]);
    }
}
