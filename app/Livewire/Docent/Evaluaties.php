<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluaties')]
class Evaluaties extends Component
{
    // Actieve tab (te doen | verlopen | afgerond).
    public string $tab = 'te doen';

    public function render()
    {
        // Stage van de docent waarvoor het evaluatie-formulier geopend kan worden.
        // (Visueel-eerst: de voorbeeldrijen linken naar deze echte stage; met
        // gekoppelde data zou elke rij naar zijn eigen stage verwijzen.)
        $evaluatieStage = auth()->user()?->docent?->stages()->first();

        return view('livewire.docent.evaluaties', [
            'evaluatieStage' => $evaluatieStage,
        ]);
    }
}
