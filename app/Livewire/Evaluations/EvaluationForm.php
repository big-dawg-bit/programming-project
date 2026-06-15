<?php

namespace App\Livewire\Evaluations;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.portal')]
class EvaluationForm extends Component
{
    public Stage $stage;
    public string $type = 'mid-term';
    public array $scores = [];

    public function mount(Stage $stage): void
    {
        $this->stage = $stage;

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

    public function submit(): void
    {
        $evaluation = Evaluation::create([
            'stage_id' => $this->stage->id,
            'framework_id' => $this->stage->framework_id,
            'type' => $this->type,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        foreach ($this->competencies() as $competency) {
            EvaluationScore::create([
                'evaluation_id' => $evaluation->id,
                'competency_id' => $competency->id,
                'weight_snapshot' => $competency->weight,
                'score' => $this->scores[$competency->id] ?? null,
            ]);
        }

        // bereken het gewogen eindcijfer uit de snapshots
        $scores = $evaluation->scores()->get();
        $totalWeight = $scores->sum('weight_snapshot');

        $overall = $totalWeight > 0
            ? $scores->sum(fn ($s) => $s->score * $s->weight_snapshot) / $totalWeight
            : 0;

        $evaluation->update(['overall_score' => round($overall, 2)]);
    }

    public function render()
    {
        return view('livewire.evaluations.evaluation-form', [
            'competencies' => $this->competencies(),
        ]);
    }
}
