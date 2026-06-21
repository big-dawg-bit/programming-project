<?php

namespace App\Livewire\Docent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn studentrapporten')]
class Rapporten extends Component
{
    // Zoekterm op studentnaam.
    public string $zoek = '';

    public function render()
    {
        $docent = auth()->user()?->docent;

        $stages = $docent
            ? $docent->stages()
                ->with(['student.user', 'company', 'weeklogs', 'finalEvaluation'])
                ->get()
            : collect();

        $rapporten = $stages->map(function ($stage) {
            // DE bindende eindbeoordeling van de docent (final_evaluation_id),
            // niet zomaar de eerste zelf-/mentorevaluatie.
            $eind = $stage->finalEvaluation;

            return [
                'naam' => $stage->student?->user?->name ?? 'Onbekende student',
                'bedrijf' => $stage->company?->name ?? '—',
                'weeklogs' => $stage->weeklogs->whereNotNull('submitted_at')->count(),
                'eindcijfer' => $eind ? number_format((float) $eind->overall_score, 1) : null,
                'resultaat' => $eind?->result,   // 'geslaagd' | 'niet_geslaagd' | null
            ];
        });

        if (trim($this->zoek) !== '') {
            $term = mb_strtolower(trim($this->zoek));
            $rapporten = $rapporten->filter(fn ($r) => str_contains(mb_strtolower($r['naam']), $term))->values();
        }

        return view('livewire.docent.rapporten', [
            'rapporten' => $rapporten,
        ]);
    }
}
