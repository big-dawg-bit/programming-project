<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn studentrapporten')]
class Rapporten extends Component
{
    public function render()
    {
        return view('livewire.docent.rapporten');
    }
}
