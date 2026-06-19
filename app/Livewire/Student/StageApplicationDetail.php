<?php

namespace App\Livewire\Student;

use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Stageaanvraag')]
class StageApplicationDetail extends Component
{
    public StageApplication $application;

    public function mount(StageApplication $application): void
    {
        // Scoping: een student kan enkel zijn eigen aanvraag bekijken.
        abort_unless($application->student_id === auth()->user()?->student?->id, 403);

        $this->application = $application->load(['company', 'agreement', 'reviews', 'stage.company']);
    }

    /** Of de aanvraag nog geannuleerd mag worden (enkel vóór goedkeuring). */
    public function getKanAnnulerenProperty(): bool
    {
        return in_array($this->application->status, ['submitted', 'changes_requested'], true);
    }

    /**
     * Annuleer (trek in) de stageaanvraag. Reviews en een eventuele overeenkomst
     * worden via de database-cascades mee verwijderd.
     */
    public function annuleer()
    {
        abort_unless($this->application->student_id === auth()->user()?->student?->id, 403);

        if (! $this->kanAnnuleren) {
            return null;
        }

        $this->application->delete();

        session()->flash('stage-melding', 'Je stageaanvraag is geannuleerd.');

        return $this->redirectRoute('student.stage', navigate: true);
    }

    public function render()
    {
        return view('livewire.student.stage-detail', [
            'application' => $this->application,
            'stage' => $this->application->stage,
            'laatsteReview' => $this->application->reviews->sortByDesc('reviewed_at')->first(),
        ]);
    }
}
