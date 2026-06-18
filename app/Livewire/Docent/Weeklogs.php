<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Weeklogs te bevestigen')]
class Weeklogs extends Component
{
    // Actieve filter (te bevestigen | bevestigd | geescaleerd).
    public string $filter = 'te bevestigen';

    /**
     * Bevestig een weeklog. Visueel-eerst: toont een melding.
     * De echte bevestig-logica zit in de back-end.
     */
    public function bevestig(string $student, int $week): void
    {
        session()->flash('weeklog-bevestigd', "Weeklog week {$week} van {$student} bevestigd.");
    }

    public function render()
    {
        return view('livewire.docent.weeklogs');
    }
}
