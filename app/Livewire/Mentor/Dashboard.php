<?php

namespace App\Livewire\Mentor;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        // Stages van de ingelogde mentor (stagiairs binnen het bedrijf).
        $mentor = auth()->user()?->mentor;

        $stages = $mentor
            ? $mentor->stages()
                ->with(['student.user', 'company'])
                ->withCount('weeklogs')
                ->get()
            : collect();

        return view('livewire.mentor.dashboard', [
            'mentor' => $mentor,
            'stages' => $stages,
        ]);
    }
}
