<?php

namespace App\Livewire\Mentor;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluatie invullen')]
class EvaluationForm extends Component
{
    public Stage $stage;
    public string $type = 'mid-term';

    // score per competency_id (0 t/m 20).
    public array $scores = [];

    public string $generalFeedback = '';
    public string $recommendations = '';

    // De labels van de twee evaluatietypes.
    public const TYPES = [
        'mid-term' => 'Tussentijdse evaluatie',
        'final' => 'Eindevaluatie',
    ];

    public function mount(Stage $stage, string $type): void
    {
        $this->stage = $stage;
        $this->type = $type;

        foreach ($this->competencies() as $competency) {
            $this->scores[$competency->id] = null;
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
     * Geef een competentie een score (0-20).
     */
    public function setScore(int $competencyId, int $score): void
    {
        $this->scores[$competencyId] = $score;
    }

    /**
     * Opslaan als concept (status 'draft').
     */
    public function saveDraft(): void
    {
        $this->persist('draft');
        session()->flash('evaluatie-opgeslagen', 'Evaluatie opgeslagen als concept.');
    }

    /**
     * Indienen (status 'submitted').
     */
    public function submit()
    {
        $this->validate([
            'scores.*' => 'required|numeric|min:0|max:20',
        ], [
            'scores.*.required' => 'Geef elke competentie een score.',
        ]);

        $this->persist('submitted');

        return redirect()->route('mentor.evaluaties');
    }

    /**
     * Maak/­update de evaluatie + scores in de database.
     */
    private function persist(string $status): void
    {
        $evaluation = Evaluation::create([
            'stage_id' => $this->stage->id,
            'framework_id' => $this->stage->framework_id,
            'type' => $this->type,
            'evaluator_role' => 'mentor',
            'status' => $status,
            'general_feedback' => $this->generalFeedback ?: null,
            'recommendations' => $this->recommendations ?: null,
            'submitted_at' => $status === 'submitted' ? now() : null,
        ]);

        foreach ($this->competencies() as $competency) {
            EvaluationScore::create([
                'evaluation_id' => $evaluation->id,
                'competency_id' => $competency->id,
                'weight_snapshot' => $competency->weight,
                'score' => $this->scores[$competency->id] ?? null,
            ]);
        }

        // Gewogen eindcijfer berekenen.
        $scores = $evaluation->scores()->get();
        $totalWeight = $scores->sum('weight_snapshot');

        $overall = $totalWeight > 0
            ? $scores->sum(fn ($s) => $s->score * $s->weight_snapshot) / $totalWeight
            : 0;

        $evaluation->update(['overall_score' => round($overall, 2)]);
    }

    public function render()
    {
        return view('livewire.mentor.evaluation-form', [
            'competencies' => $this->competencies(),
            'typeLabel' => self::TYPES[$this->type] ?? $this->type,
        ]);
    }
}
