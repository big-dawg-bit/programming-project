<?php

namespace App\Livewire\Evaluations;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluatie invullen')]
class EvaluationForm extends Component
{
    public Stage $stage;

    // Welk evaluatietype wordt ingevuld: 'mid-term' (tussentijds) of 'final' (eind).
    public string $type = 'mid-term';

    // Scores per competentie-id (0-100).
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

    // Reeds ingediende evaluaties voor deze stage (zodat mid-term en final naast elkaar zichtbaar zijn).
    public function bestaandeEvaluaties()
    {
        return $this->stage->evaluations()->latest('submitted_at')->get();
    }

    public function submit(): void
    {
        $this->validate([
            'type' => 'required|in:mid-term,final',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ], [
            'scores.*.numeric' => 'Een score moet een getal zijn.',
            'scores.*.min' => 'Een score kan niet lager zijn dan 0.',
            'scores.*.max' => 'Een score kan niet hoger zijn dan 100.',
        ]);

        // Voorkom een tweede evaluatie van hetzelfde type op dezelfde stage.
        $bestaatAl = Evaluation::where('stage_id', $this->stage->id)
            ->where('type', $this->type)
            ->exists();

        if ($bestaatAl) {
            $this->addError('type', 'Er bestaat al een '.$this->typeLabel($this->type).'-evaluatie voor deze stage.');

            return;
        }

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

        // Bereken het gewogen eindcijfer uit de vastgelegde gewichten (snapshots).
        $scores = $evaluation->scores()->get();
        $totalWeight = $scores->sum('weight_snapshot');

        $overall = $totalWeight > 0
            ? $scores->sum(fn ($s) => $s->score * $s->weight_snapshot) / $totalWeight
            : 0;

        $evaluation->update(['overall_score' => round($overall, 2)]);

        // Formulier leegmaken voor een eventuele volgende evaluatie + bevestiging tonen.
        foreach ($this->competencies() as $competency) {
            $this->scores[$competency->id] = null;
        }

        session()->flash('success', $this->typeLabel($this->type).'-evaluatie opgeslagen (eindcijfer: '.number_format((float) $evaluation->overall_score, 1).').');
    }

    public function typeLabel(string $type): string
    {
        return $type === 'final' ? 'Eind' : 'Tussentijdse';
    }

    public function render()
    {
        return view('livewire.evaluations.evaluation-form', [
            'competencies' => $this->competencies(),
            'bestaande' => $this->bestaandeEvaluaties(),
        ]);
    }
}
