<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class StudentDetail extends Component
{
    // Naam van de bekeken student (via de route doorgegeven).
    public string $naam = 'Lina Janssens';

    // Actieve tab (weeklogs | evaluaties | documenten | rapport).
    public string $tab = 'weeklogs';

    public function mount(string $naam = 'Lina Janssens'): void
    {
        $this->naam = urldecode($naam);
    }

    public function render()
    {
        // De student-naam in de topbar tonen (zoals in het Figma-ontwerp).
        return view('livewire.docent.student-detail')
            ->layoutData(['title' => $this->naam]);
    }
}
