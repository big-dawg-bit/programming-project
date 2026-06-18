<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn stageaanvraag')]
class StageOverview extends Component
{
    public function render()
    {
        // Scoping: de (laatste) stage van de ingelogde student. Student A ziet nooit die van B.
        $student = auth()->user()?->student;

        $stage = $student
            ? $student->stages()->with('company')->latest()->first()
            : null;

        return view('livewire.student.stage', [
            'stage' => $stage,
        ]);
    }
}
