<?php

namespace App\Livewire\Evaluations;

use App\Models\Stage;
use Livewire\Component;

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

    public function render()
    {
        return view('livewire.evaluations.evaluation-form', [
            'competencies' => $this->competencies(),
        ]);
    }
}
