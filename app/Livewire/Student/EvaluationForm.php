<?php

namespace App\Livewire\Student;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Zelfevaluatie invullen')]
class EvaluationForm extends Component
{
    public Stage $stage;
    public string $type = 'final';

    /** Score per competency_id (0 t/m 5). */
    public array $scores = [];

    /** Beschrijving per competency_id (de "Studentenbeschrijving"). */
    public array $descriptions = [];

    public const TYPES = [
        'mid-term' => 'Tussentijdse zelfevaluatie',
        'final' => 'Eindzelfevaluatie',
    ];

    public function mount(Stage $stage, string $type = 'final'): void
    {
        // Scoping: enkel de student van deze stage mag zichzelf evalueren.
        abort_unless($stage->student?->user_id === auth()->id(), 403);

        // Enkel geldige types toelaten.
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $this->stage = $stage;
        $this->type = $type;

        foreach ($this->competencies() as $competency) {
            $this->scores[$competency->id] = null;
            $this->descriptions[$competency->id] = '';
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
        session()->flash('evaluatie-opgeslagen', 'Zelfevaluatie opgeslagen als concept.');
    }

    public function submit()
    {
        $this->validate([
            'scores.*' => 'required|integer|min:0|max:5',
            'descriptions.*' => 'nullable|string|max:2000',
        ], [
            'scores.*.required' => 'Geef elke competentie een score.',
        ]);

        $this->persist('submitted');

        session()->flash('status', 'Zelfevaluatie ingediend.');

        return redirect()->route('student.evaluaties');
    }

    private function persist(string $status): void
    {
        $evaluation = Evaluation::create([
            'stage_id' => $this->stage->id,
            'framework_id' => $this->stage->framework_id,
            'type' => $this->type,
            'evaluator_role' => 'student',
            'status' => $status,
            'submitted_at' => $status === 'submitted' ? now() : null,
        ]);

        foreach ($this->competencies() as $competency) {
            EvaluationScore::create([
                'evaluation_id' => $evaluation->id,
                'competency_id' => $competency->id,
                'weight_snapshot' => $competency->weight,
                'score' => $this->scores[$competency->id] ?? null,
                'student_description' => $this->descriptions[$competency->id] ?: null,
            ]);
        }

        // Gewogen totaal (zelfde formule als mentor/docent).
        $scores = $evaluation->scores()->get();
        $totalWeight = $scores->sum('weight_snapshot');

        $overall = $totalWeight > 0
            ? $scores->sum(fn ($s) => $s->score * $s->weight_snapshot) / $totalWeight
            : 0;

        $evaluation->update(['overall_score' => round($overall, 2)]);
    }

    public function render()
    {
        return view('livewire.student.evaluation-form', [
            'competencies' => $this->competencies(),
            'typeLabel' => self::TYPES[$this->type] ?? $this->type,
        ]);
    }
}
