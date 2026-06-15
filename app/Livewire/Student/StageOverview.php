<?php

namespace App\Livewire\Student;

use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn stageaanvraag')]
class StageOverview extends Component
{
    public function render()
    {
        // Voorlopig de eerste stage (testdata). Later: stage van de ingelogde student.
        $stage = Stage::with('company')->first();

        return view('livewire.student.stage', [
            'stage' => $stage,
        ]);
    }
}
