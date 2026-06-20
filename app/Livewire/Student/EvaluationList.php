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

    /** De meest recente stage van de ingelogde student. */
    protected function stage()
    {
        return auth()->user()->student?->stages()->latest('id')->first();
    }

    public function evaluations(): Collection
    {
        $student = auth()->user()->student;

        if (! $student) {
            return collect();
        }

        // Scoping: enkel evaluaties van de eigen stages, en enkel ingediende.
        return Evaluation::query()
            ->whereIn('stage_id', $student->stages()->pluck('id'))
            ->where('type', $this->typeForTab())
            ->where('status', 'submitted')
            ->with(['scores.competency', 'stage.company'])
            ->latest('submitted_at')
            ->get();
    }

    /**
     * Gecombineerde eindevaluatie: per competentie de student-, mentor- én
     * docentscore naast elkaar, plus de bindende eindbeoordeling van de docent.
     */
    public function combined(): array
    {
        $stage = $this->stage();

        if (! $stage || ! $stage->framework) {
            return ['rows' => collect(), 'studentEval' => null, 'mentorEval' => null, 'docentEval' => null, 'finalEval' => null];
        }

        $type = $this->typeForTab();

        $find = fn (string $role) => Evaluation::query()
            ->where('stage_id', $stage->id)
            ->where('evaluator_role', $role)
            ->where('type', $type)
            ->where('status', 'submitted')
            ->with('scores')
            ->latest('submitted_at')
            ->first();

        $studentEval = $find('student');
        $mentorEval = $find('mentor');
        $docentEval = $find('docent');

        // De officiële, bindende eindbeoordeling (door de docent vastgelegd op de stage).
        $finalEval = $stage->finalEvaluation;

        $rows = $stage->framework->competencies()->orderBy('sort_order')->get()
            ->map(function ($comp) use ($studentEval, $mentorEval, $docentEval) {
                $s = $studentEval?->scores->firstWhere('competency_id', $comp->id);
                $m = $mentorEval?->scores->firstWhere('competency_id', $comp->id);
                $d = $docentEval?->scores->firstWhere('competency_id', $comp->id);

                return [
                    'competency' => $comp,
                    'studentDescription' => $s?->student_description,
                    'mentorFeedback' => $m?->feedback,
                    'studentScore' => $s?->score,
                    'mentorScore' => $m?->score,
                    'docentScore' => $d?->score,
                    'docentFeedback' => $d?->feedback,
                ];
            });

        return ['rows' => $rows, 'studentEval' => $studentEval, 'mentorEval' => $mentorEval, 'docentEval' => $docentEval, 'finalEval' => $finalEval];
    }

    public function render()
    {
        return view('livewire.student.evaluations', [
            'evaluations' => $this->evaluations(),
            'stage' => $this->stage(),
            'combined' => $this->combined(),
        ]);
    }
}
