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
        $stage = auth()->user()->student
            ?->stages()
            ->with('company')
            ->latest()
            ->first();
        return view('livewire.student.stage', [
            'stage' => $stage,
        ]);
    }
}
