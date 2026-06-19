<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn stages')]
class StageOverview extends Component
{
    public function render()
    {
        $student = auth()->user()?->student;

        // Alle stageaanvragen van de ingelogde student. Per aanvraag klikt de
        // student door naar het detail (status, overeenkomst, checklist).
        $applications = $student
            ? $student->applications()
                ->with(['company', 'agreement', 'stage'])
                ->latest()
                ->get()
            : collect();

        return view('livewire.student.stage', [
            'applications' => $applications,
        ]);
    }
}
