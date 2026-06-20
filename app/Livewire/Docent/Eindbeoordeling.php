<?php

namespace App\Livewire\Docent;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Eindbeoordeling')]
class Eindbeoordeling extends Component
{
    /** Vanaf welk /20-cijfer is de student geslaagd (Belgisch: 10/20). */
    private const GESLAAGD_VANAF = 10.0;

    public Stage $stage;

    /** Definitieve score per competency_id (Belgisch, /20). */
    public array $scores = [];

    /** Onderbouwing per competency_id. */
    public array $feedback = [];

    /** Geschreven eindconclusie van de docent. */
    public string $conclusion = '';

    public function mount(Stage $stage): void
    {
        // Scoping: enkel de TOEGEWEZEN docent van deze stage geeft de eindbeoordeling.
        $user = auth()->user();
        abort_unless(
            $user->hasRole('docent') && $stage->docent_id === $user->docent?->id,
            403
        );

        $this->stage = $stage;

        foreach ($this->competencies() as $competency) {
            $this->scores[$competency->id] = null;
            $this->feedback[$competency->id] = '';
        }
    }

    public function competencies()
    {
        return $this->stage->framework
            ->competencies()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Live gewogen totaal op /100 en geslaagd-status uit de ingevulde scores.
     * Geeft null zolang nog niet elke competentie een score heeft.
     */
    public function liveResult(): array
    {
        $competencies = $this->competencies();

        $allFilled = $competencies->isNotEmpty()
            && $competencies->every(fn ($c) => is_numeric($this->scores[$c->id] ?? null));

        if (! $allFilled) {
            return ['total100' => null, 'passed' => null];
        }

        $totalWeight = (float) $competencies->sum('weight');
        $weighted = $competencies->sum(fn ($c) => (float) $this->scores[$c->id] * $c->weight);

        $overall20 = $totalWeight > 0 ? $weighted / $totalWeight : 0.0;

        return [
            'total100' => round($overall20 * 5, 1),
            'passed' => $overall20 >= self::GESLAAGD_VANAF,
        ];
    }

    /** Ingediende eind-evaluatie van een rol (student/mentor) als read-only referentie. */
    private function contextEvaluation(string $role): ?Evaluation
    {
        return Evaluation::with('scores')
            ->where('stage_id', $this->stage->id)
            ->where('evaluator_role', $role)
            ->where('type', 'final')
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->first();
    }

    public function submit(): void
    {
        $this->validate([
            'scores.*' => 'required|numeric|min:0|max:20',
            'feedback.*' => 'nullable|string|max:2000',
            'conclusion' => 'nullable|string|max:5000',
        ], [
            'scores.*.required' => 'Geef elke competentie een definitieve score.',
        ]);

        $evaluation = Evaluation::create([
            'stage_id' => $this->stage->id,
            'framework_id' => $this->stage->framework_id,
            'type' => 'final',
            'evaluator_role' => 'docent',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        foreach ($this->competencies() as $competency) {
            EvaluationScore::create([
                'evaluation_id' => $evaluation->id,
                'competency_id' => $competency->id,
                'weight_snapshot' => $competency->weight,
                'score' => $this->scores[$competency->id] ?? null,
                'feedback' => $this->feedback[$competency->id] ?: null,
            ]);
        }

        // Gewogen eindcijfer (/20) uit de snapshots.
        $scores = $evaluation->scores()->get();
        $totalWeight = $scores->sum('weight_snapshot');

        $overall = $totalWeight > 0
            ? $scores->sum(fn ($s) => $s->score * $s->weight_snapshot) / $totalWeight
            : 0;

        $evaluation->update([
            'overall_score' => round($overall, 2),
            'conclusion' => $this->conclusion ?: null,
            'result' => $overall >= self::GESLAAGD_VANAF ? 'geslaagd' : 'niet_geslaagd',
        ]);

        // Dit is DE bindende eindbeoordeling: koppel ze als officieel resultaat aan de stage.
        $this->stage->update(['final_evaluation_id' => $evaluation->id]);

        session()->flash('status', 'Eindbeoordeling opgeslagen.');
    }

    public function render()
    {
        return view('livewire.docent.eindbeoordeling', [
            'competencies' => $this->competencies(),
            'studentEval' => $this->contextEvaluation('student'),
            'mentorEval' => $this->contextEvaluation('mentor'),
            'result' => $this->liveResult(),
        ]);
    }
}
