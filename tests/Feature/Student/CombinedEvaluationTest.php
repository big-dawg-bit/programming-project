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

function makeCombinedSetup(): array
{
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);

    $framework = CompetencyFramework::create(['name' => 'Kader', 'version' => 1, 'is_active' => true]);
    $compA = Competency::create(['framework_id' => $framework->id, 'code' => 'D1', 'title' => 'Planning', 'weight' => 50, 'sort_order' => 1]);
    $compB = Competency::create(['framework_id' => $framework->id, 'code' => 'D2', 'title' => 'Communicatie', 'weight' => 50, 'sort_order' => 2]);

    $stage = Stage::create(['student_id' => $student->id, 'framework_id' => $framework->id, 'status' => 'active']);

    return [$user, $stage, $compA, $compB];
}

/** $scores = [competency_id => [score, tekst]]. */
function makeRoleEvaluation(Stage $stage, string $role, array $scores): Evaluation
{
    $eval = Evaluation::create([
        'stage_id' => $stage->id,
        'framework_id' => $stage->framework_id,
        'type' => 'final',
        'evaluator_role' => $role,
        'status' => 'submitted',
        'submitted_at' => now(),
        'overall_score' => 0,
    ]);

    foreach ($scores as $compId => [$score, $text]) {
        EvaluationScore::create([
            'evaluation_id' => $eval->id,
            'competency_id' => $compId,
            'weight_snapshot' => 50,
            'score' => $score,
            'student_description' => $role === 'student' ? $text : null,
            'feedback' => $role === 'mentor' ? $text : null,
        ]);
    }

    return $eval;
}

it('toont de gecombineerde eindevaluatie met student- en mentorscore naast elkaar', function () {
    [$user, $stage, $compA, $compB] = makeCombinedSetup();

    makeRoleEvaluation($stage, 'student', [
        $compA->id => [3, 'Mijn planning verliep vlot.'],
        $compB->id => [4, 'Goed gecommuniceerd.'],
    ]);
    makeRoleEvaluation($stage, 'mentor', [
        $compA->id => [4, 'Sterke planning.'],
        $compB->id => [5, 'Heldere communicatie.'],
    ]);

    Livewire::actingAs($user)->test(EvaluationList::class)
        ->set('tab', 'eind')
        ->assertSee('Planning')
        ->assertSee('Communicatie')
        ->assertSee('Mijn planning verliep vlot.')
        ->assertSee('Sterke planning.')
        ->assertSee('3/5')
        ->assertSee('5/5');
});

it('toont een lege eindevaluatie wanneer student noch mentor ingediend heeft', function () {
    [$user] = makeCombinedSetup();

    Livewire::actingAs($user)->test(EvaluationList::class)
        ->set('tab', 'eind')
        ->assertSee('Nog geen eindevaluatie');
});
