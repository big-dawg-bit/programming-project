<?php

namespace App\Livewire\Student;

use App\Models\Evaluation;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn evaluaties')]
class EvaluationList extends Component
{
    /** Actieve tab (tussentijds | eind). */
    public string $tab = 'tussentijds';

    /** Vertaalt de zichtbare tab naar het opgeslagen type in de database. */
    protected function typeForTab(): string
    {
        return $this->tab === 'eind' ? 'final' : 'mid-term';
    }

    public function evaluations(): Collection
    {
        $student = auth()->user()->student;

        if (! $student) {
            return collect();
        }

        // Scoping: enkel evaluaties van de eigen stages, en enkel ingediende
        // (drafts van mentor/docent blijven verborgen voor de student).
        return Evaluation::query()
            ->whereIn('stage_id', $student->stages()->pluck('id'))
            ->where('type', $this->typeForTab())
            ->where('status', 'submitted')
            ->with(['scores.competency', 'stage.company'])
            ->latest('submitted_at')
            ->get();
    }

    public function render()
    {
        $student = auth()->user()->student;

        return view('livewire.student.evaluations', [
            'evaluations' => $this->evaluations(),
            // De meest recente stage van de student, om de zelfevaluatie te openen.
            'stage' => $student?->stages()->latest('id')->first(),
        ]);
    }
}
