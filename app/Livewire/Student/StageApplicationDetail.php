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

    public function render()
    {
        return view('livewire.student.stage-detail', [
            'application' => $this->application,
            'stage' => $this->application->stage,
            'laatsteReview' => $this->application->reviews->sortByDesc('reviewed_at')->first(),
        ]);
    }
}
