<?php

namespace App\Livewire\Student;

use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn stage')]
class StageOverview extends Component
{
    public function render()
    {
        $student = auth()->user()?->student;

        // De (laatste) stageaanvraag van de ingelogde student. Alles op dit
        // scherm wordt hieruit afgeleid: de status, de overeenkomst en de stage.
        $application = $student
            ? $student->applications()
                ->with(['company', 'agreement', 'reviews', 'stage.company'])
                ->latest()
                ->first()
            : null;

        return view('livewire.student.stage', [
            'application' => $application,
            'stage' => $application?->stage,
            'laatsteReview' => $application?->reviews->sortByDesc('reviewed_at')->first(),
        ]);
    }
}
