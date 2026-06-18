<?php

namespace App\Livewire\Docent;

use App\Models\StageAgreement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    // De stage waarvoor de docent de overeenkomst tekent (null = modal dicht).
    public ?int $signingStageId = null;

    public function openSign(int $stageId): void
    {
        $this->signingStageId = $stageId;
    }

    public function closeSign(): void
    {
        $this->signingStageId = null;
    }

    /**
     * Sla de op de trackpad getekende docent-handtekening op de overeenkomst op.
     */
    public function signDocent(string $signature): void
    {
        $docent = auth()->user()?->docent;
        abort_unless($docent !== null && $this->signingStageId !== null, 403);

        validator(['signature' => $signature], [
            'signature' => 'required|string|starts_with:data:image/png;base64,|max:500000',
        ])->validate();

        // Scoping: enkel een eigen stage met een bestaande overeenkomst.
        $stage = $docent->stages()->whereNotNull('application_id')->findOrFail($this->signingStageId);
        $agreement = StageAgreement::where('application_id', $stage->application_id)->firstOrFail();

        if ($agreement->status !== 'bevestigd') {
            $agreement->fill([
                'docent_signature' => $signature,
                'docent_signed_at' => now(),
            ])->save();

            // Status volgt uit beide handtekeningen (student + docent).
            $agreement->syncSignatureStatus();

            session()->flash('docent-signed', 'Je handtekening is opgeslagen.');
        }

        $this->closeSign();
    }

    public function render()
    {
        // Stages die door de ingelogde docent begeleid worden.
        $docent = auth()->user()?->docent;

        $stages = $docent
            ? $docent->stages()
                ->with(['student.user', 'company', 'application.agreement'])
                ->withCount(['weeklogs', 'evaluations'])
                ->get()
            : collect();

        // Stages die nog geen evaluatie hebben.
        $teEvalueren = $stages->filter(fn ($stage) => $stage->evaluations_count === 0)->count();

        return view('livewire.docent.dashboard', [
            'stages' => $stages,
            'teEvalueren' => $teEvalueren,
        ]);
    }
}
