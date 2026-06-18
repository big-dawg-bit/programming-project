<?php

namespace App\Livewire\Mentor;

use App\Models\Evaluation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluatie bekijken')]
class EvaluationDetail extends Component
{
    public Evaluation $evaluation;

    public const TYPES = [
        'mid-term' => 'Tussentijdse evaluatie',
        'final' => 'Eindevaluatie',
    ];

    public function mount(Evaluation $evaluation): void
    {
        $this->evaluation = $evaluation->load('scores.competency', 'stage.student.user', 'stage.company');
    }

    public function render()
    {
        return view('livewire.mentor.evaluation-detail', [
            'typeLabel' => self::TYPES[$this->evaluation->type] ?? $this->evaluation->type,
        ]);
    }
}
