<?php

namespace App\Livewire\Mentor;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Mentor;
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

    public const TYPES = [
        'mid-term' => 'Tussentijdse evaluatie',
        'final' => 'Eindevaluatie',
    ];

    public function mount(Stage $stage, string $type): void
    {
        // Scoping: enkel de mentor die aan DEZE stage is toegewezen mag evalueren.
        $mentor = Mentor::where('user_id', Auth::id())->first();
        abort_unless($mentor && $stage->mentor_id === $mentor->id, 403);

        // Enkel geldige types.
        abort_unless(array_key_exists($type, self::TYPES), 404);

        // Zonder evaluatiekader kan er niet geëvalueerd worden (anders crasht competencies()).
        abort_unless($stage->framework !== null, 404);

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

    public function setScore(int $competencyId, int $score): void
    {
        $this->scores[$competencyId] = $score;
    }

    public function saveDraft(): void
    {
        $this->persist('draft');
        session()->flash('evaluatie-opgeslagen', 'Evaluatie opgeslagen als concept.');
    }

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
     * Eén evaluatie per (stage + type + rol): updateOrCreate i.p.v. altijd nieuw,
     * zodat concept → indienen geen dubbele rijen maakt.
     */
    private function persist(string $status): void
    {
        $evaluation = Evaluation::updateOrCreate(
            [
                'stage_id' => $this->stage->id,
                'type' => $this->type,
                'evaluator_role' => 'mentor',
            ],
            [
                'framework_id' => $this->stage->framework_id,
                'status' => $status,
                'general_feedback' => $this->generalFeedback ?: null,
                'recommendations' => $this->recommendations ?: null,
                'submitted_at' => $status === 'submitted' ? now() : null,
            ]
        );

        // Scores opnieuw opbouwen.
        $evaluation->scores()->delete();

        foreach ($this->competencies() as $competency) {
            EvaluationScore::create([
                'evaluation_id' => $evaluation->id,
                'competency_id' => $competency->id,
                'weight_snapshot' => $competency->weight,
                'score' => $this->scores[$competency->id] ?? null,
            ]);
        }

        // Gewogen eindcijfer.
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
