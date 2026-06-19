<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluaties')]
class Evaluaties extends Component
{
    public function render()
    {
        $docent = auth()->user()?->docent;

        $stages = $docent
            ? $docent->stages()
                ->with(['student.user', 'company', 'evaluations'])
                ->get()
            : collect();

        return view('livewire.docent.evaluaties', [
            'stages' => $stages,
        ]);
    }
}
