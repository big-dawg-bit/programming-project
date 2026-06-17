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
        return view('livewire.docent.evaluaties');
    }
}
