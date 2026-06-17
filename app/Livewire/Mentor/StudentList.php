<?php

namespace App\Livewire\Mentor;

use App\Models\Mentor;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn studenten')]
class StudentList extends Component
{
    /**
     * De mentor die hoort bij de ingelogde gebruiker.
     */
    private function currentMentor(): ?Mentor
    {
        return Mentor::where('user_id', Auth::id())->first();
    }

    public function render()
    {
        $mentor = $this->currentMentor();

        // Alle stages (en dus studenten) van deze mentor, met tellingen erbij.
        $stages = $mentor
            ? $mentor->stages()
                ->with(['student.user'])
                ->withCount([
                    'weeklogs',
                    'weeklogs as goedgekeurd_count' => fn ($q) => $q->whereIn('status', ['goedgekeurd', 'gevalideerd']),
                    'weeklogs as te_beoordelen_count' => fn ($q) => $q->where('status', 'ingediend'),
                ])
                ->get()
            : collect();

        return view('livewire.mentor.student-list', [
            'stages' => $stages,
        ]);
    }
}
