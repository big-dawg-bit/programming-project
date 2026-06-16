<?php

use App\Livewire\Student\EvaluationList;
use App\Models\Competency;
use App\Models\CompetencyFramework;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

/**
 * Maakt een ingelogde student met een actieve stage + framework (2 competenties).
 * Geeft [User, Student, Stage, Competency A, Competency B] terug.
 */
function makeStudentStageForList(): array
{
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);

    $framework = CompetencyFramework::create([
        'name' => 'Testkader',
        'version' => 1,
        'is_active' => true,
    ]);

    $compA = Competency::create(['framework_id' => $framework->id, 'code' => 'A', 'title' => 'Competentie A', 'weight' => 50]);
    $compB = Competency::create(['framework_id' => $framework->id, 'code' => 'B', 'title' => 'Competentie B', 'weight' => 50]);

    $stage = Stage::create([
        'student_id' => $student->id,
        'framework_id' => $framework->id,
        'status' => 'active',
    ]);

    return [$user, $student, $stage, $compA, $compB];
}

/** Maakt een evaluatie + scores op een stage. $scores = [competency_id => [weight, score]]. */
function makeListEvaluation(Stage $stage, array $attrs = [], array $scores = []): Evaluation
{
    $evaluation = Evaluation::create(array_merge([
        'stage_id' => $stage->id,
        'framework_id' => $stage->framework_id,
        'type' => 'mid-term',
        'status' => 'submitted',
        'submitted_at' => now(),
        'overall_score' => 0,
    ], $attrs));

    foreach ($scores as $competencyId => [$weight, $score]) {
        EvaluationScore::create([
            'evaluation_id' => $evaluation->id,
            'competency_id' => $competencyId,
            'weight_snapshot' => $weight,
            'score' => $score,
        ]);
    }

    return $evaluation;
}

it('toont enkel de ingediende evaluaties van de eigen student', function () {
    [$user, , $stage] = makeStudentStageForList();

    // eigen + ingediend -> zichtbaar
    makeListEvaluation($stage, ['type' => 'mid-term', 'status' => 'submitted']);
    // eigen + draft -> verborgen
    makeListEvaluation($stage, ['type' => 'mid-term', 'status' => 'draft']);
    // andere student -> verborgen
    [, , $otherStage] = makeStudentStageForList();
    makeListEvaluation($otherStage, ['type' => 'mid-term', 'status' => 'submitted']);

    $evaluations = Livewire::actingAs($user)->test(EvaluationList::class)
        ->set('tab', 'tussentijds')
        ->viewData('evaluations');

    expect($evaluations)->toHaveCount(1);
});

it('filtert op tussentijds versus eind via de tab', function () {
    [$user, , $stage] = makeStudentStageForList();
    makeListEvaluation($stage, ['type' => 'mid-term']);
    makeListEvaluation($stage, ['type' => 'final']);

    $component = Livewire::actingAs($user)->test(EvaluationList::class);

    $mid = $component->set('tab', 'tussentijds')->viewData('evaluations');
    expect($mid)->toHaveCount(1)
        ->and($mid->first()->type)->toBe('mid-term');

    $eind = $component->set('tab', 'eind')->viewData('evaluations');
    expect($eind)->toHaveCount(1)
        ->and($eind->first()->type)->toBe('final');
});

it('toont het gewogen eindcijfer en de competenties', function () {
    [$user, , $stage, $compA, $compB] = makeStudentStageForList();

    makeListEvaluation($stage, ['type' => 'mid-term', 'overall_score' => 15.5], [
        $compA->id => [50, 14],
        $compB->id => [50, 17],
    ]);

    Livewire::actingAs($user)->test(EvaluationList::class)
        ->set('tab', 'tussentijds')
        ->assertSee('15.5')
        ->assertSee('Competentie A')
        ->assertSee('Competentie B');
});

it('toont een lege staat wanneer er geen evaluaties zijn', function () {
    [$user] = makeStudentStageForList();

    Livewire::actingAs($user)->test(EvaluationList::class)
        ->set('tab', 'eind')
        ->assertSee('Nog geen eindevaluatie');
});
