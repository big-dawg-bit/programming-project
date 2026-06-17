<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Toegewezen studenten')]
class Studenten extends Component
{
    // Actieve filter op weeklog-status (alle | goedgekeurd | te bevestigen | aandacht).
    public string $filter = 'alle';

    public function render()
    {
        return view('livewire.docent.studenten');
    }
}
