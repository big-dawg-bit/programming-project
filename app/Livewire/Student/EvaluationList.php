<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn evaluaties')]
class EvaluationList extends Component
{
    // Actieve tab (tussentijds | eind).
    public string $tab = 'tussentijds';

    public function render()
    {
        return view('livewire.student.evaluations');
    }
}
