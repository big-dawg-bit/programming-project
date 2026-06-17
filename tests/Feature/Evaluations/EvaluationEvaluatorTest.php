<?php

use App\Livewire\Evaluations\EvaluationForm;
use App\Models\Evaluation;
use App\Models\Stage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(); // rollen + framework + competenties + stage
    $this->stage = Stage::whereNotNull('framework_id')->first();
});

it('laat een docent een evaluatie indienen', function () {
    $docent = User::factory()->withRole('docent')->create();

    Livewire::actingAs($docent)->test(EvaluationForm::class, ['stage' => $this->stage])
        ->call('submit');

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'docent')->first();

    expect($evaluation)->not->toBeNull();
    expect($evaluation->status)->toBe('submitted');
});

it('laat een mentor onafhankelijk een evaluatie indienen', function () {
    $mentor = User::factory()->withRole('mentor')->create();

    Livewire::actingAs($mentor)->test(EvaluationForm::class, ['stage' => $this->stage])
        ->call('submit');

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'mentor')->first();

    expect($evaluation)->not->toBeNull();
    expect($evaluation->status)->toBe('submitted');
});

it('docent en mentor evalueren onafhankelijk op dezelfde stage', function () {
    $docent = User::factory()->withRole('docent')->create();
    $mentor = User::factory()->withRole('mentor')->create();

    Livewire::actingAs($docent)->test(EvaluationForm::class, ['stage' => $this->stage])->call('submit');
    Livewire::actingAs($mentor)->test(EvaluationForm::class, ['stage' => $this->stage])->call('submit');

    expect(Evaluation::where('stage_id', $this->stage->id)->where('evaluator_role', 'docent')->count())->toBe(1);
    expect(Evaluation::where('stage_id', $this->stage->id)->where('evaluator_role', 'mentor')->count())->toBe(1);
});
