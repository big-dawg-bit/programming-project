<?php

use App\Livewire\Evaluations\EvaluationForm;
use App\Models\Evaluation;
use App\Models\Stage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(); // rollen + framework + competenties + stage (met toegewezen docent + mentor)
    $this->stage = Stage::whereNotNull('framework_id')->first();
});

it('laat de toegewezen docent een evaluatie indienen', function () {
    Livewire::actingAs($this->stage->docent->user)
        ->test(EvaluationForm::class, ['stage' => $this->stage])
        ->call('submit');

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'docent')->first();

    expect($evaluation)->not->toBeNull();
    expect($evaluation->status)->toBe('submitted');
});

it('laat de toegewezen mentor onafhankelijk een evaluatie indienen', function () {
    Livewire::actingAs($this->stage->mentor->user)
        ->test(EvaluationForm::class, ['stage' => $this->stage])
        ->call('submit');

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'mentor')->first();

    expect($evaluation)->not->toBeNull();
    expect($evaluation->status)->toBe('submitted');
});

it('toegewezen docent en mentor evalueren onafhankelijk op dezelfde stage', function () {
    Livewire::actingAs($this->stage->docent->user)
        ->test(EvaluationForm::class, ['stage' => $this->stage])->call('submit');
    Livewire::actingAs($this->stage->mentor->user)
        ->test(EvaluationForm::class, ['stage' => $this->stage])->call('submit');

    expect(Evaluation::where('stage_id', $this->stage->id)->where('evaluator_role', 'docent')->count())->toBe(1);
    expect(Evaluation::where('stage_id', $this->stage->id)->where('evaluator_role', 'mentor')->count())->toBe(1);
});
