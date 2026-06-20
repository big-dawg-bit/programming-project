<?php

use App\Livewire\Docent\Evaluaties;
use App\Livewire\Mentor\EvaluationForm;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed();
});

it('geeft de docent per student een link naar de eindbeoordeling', function () {
    $docent = User::where('email', 'docent@ehb.be')->first();
    $stage = $docent->docent->stages()->first();

    expect($stage)->not->toBeNull();

    Livewire::actingAs($docent)
        ->test(Evaluaties::class)
        ->assertSee('Beoordelen')
        ->assertSeeHtml(route('docent.eindbeoordeling', $stage));
});

it('toont de docent dat de zelfevaluatie is ingediend', function () {
    $docent = User::where('email', 'docent@ehb.be')->first();
    $stage = $docent->docent->stages()->first();

    // Zelfevaluatie van de student als ingediend in de DB zetten.
    $eval = Evaluation::create([
        'stage_id' => $stage->id,
        'framework_id' => $stage->framework_id,
        'type' => 'final',
        'evaluator_role' => 'student',
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);
    foreach ($stage->framework->competencies as $c) {
        EvaluationScore::create([
            'evaluation_id' => $eval->id,
            'competency_id' => $c->id,
            'weight_snapshot' => $c->weight,
            'score' => 15,
        ]);
    }

    Livewire::actingAs($docent)
        ->test(Evaluaties::class)
        ->assertSee('ingediend');
});

it('laat enkel de toegewezen mentor het evaluatieformulier openen', function () {
    $stage = Stage::whereNotNull('mentor_id')->whereNotNull('framework_id')->first();

    // Toegewezen mentor mag.
    Livewire::actingAs($stage->mentor->user)
        ->test(EvaluationForm::class, ['stage' => $stage, 'type' => 'final'])
        ->assertOk();

    // Een andere mentor niet.
    $andere = User::where('email', 'mentor-bedrijf@bedrijf.be')->first();
    Livewire::actingAs($andere)
        ->test(EvaluationForm::class, ['stage' => $stage, 'type' => 'final'])
        ->assertForbidden();
});

it('maakt geen dubbele mentor-evaluatie bij concept daarna indienen', function () {
    $stage = Stage::whereNotNull('mentor_id')->whereNotNull('framework_id')->first();
    $scores = $stage->framework->competencies->mapWithKeys(fn ($c) => [$c->id => 14])->all();

    $form = Livewire::actingAs($stage->mentor->user)
        ->test(EvaluationForm::class, ['stage' => $stage, 'type' => 'final']);

    foreach ($scores as $id => $val) {
        $form->set("scores.{$id}", $val);
    }

    $form->call('saveDraft')->call('submit');

    $count = Evaluation::where('stage_id', $stage->id)
        ->where('type', 'final')->where('evaluator_role', 'mentor')->count();

    expect($count)->toBe(1);
});
