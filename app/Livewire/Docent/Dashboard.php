<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        // Stages die door de ingelogde docent begeleid worden.
        $docent = auth()->user()?->docent;

        $stages = $docent
            ? $docent->stages()
                ->with(['student.user', 'company', 'application.agreement'])
                ->withCount(['weeklogs', 'evaluations'])
                ->get()
            : collect();

        // Stages die nog geen evaluatie hebben.
        $teEvalueren = $stages->filter(fn ($stage) => $stage->evaluations_count === 0)->count();

        return view('livewire.docent.dashboard', [
            'stages' => $stages,
            'teEvalueren' => $teEvalueren,
        ]);
    }
}
