<?php

use App\Livewire\Student\EvaluationForm;
use App\Models\Competency;
use App\Models\CompetencyFramework;
use App\Models\Evaluation;
use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

/** Student met stage + framework (2 competenties). Geeft [user, stage, compA, compB] terug. */
function makeStudentSelfEval(): array
{
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);

    $framework = CompetencyFramework::create([
        'name' => 'Testkader',
        'version' => 1,
        'is_active' => true,
    ]);

    $compA = Competency::create(['framework_id' => $framework->id, 'code' => 'A', 'title' => 'Competentie A', 'weight' => 60, 'sort_order' => 1]);
    $compB = Competency::create(['framework_id' => $framework->id, 'code' => 'B', 'title' => 'Competentie B', 'weight' => 40, 'sort_order' => 2]);

    $stage = Stage::create([
        'student_id' => $student->id,
        'framework_id' => $framework->id,
        'status' => 'active',
    ]);

    return [$user, $stage, $compA, $compB];
}

it('laat de student een zelfevaluatie indienen met scores en beschrijving', function () {
    [$user, $stage, $compA, $compB] = makeStudentSelfEval();

    Livewire::actingAs($user)
        ->test(EvaluationForm::class, ['stage' => $stage, 'type' => 'final'])
        ->call('setScore', $compA->id, 5)
        ->call('setScore', $compB->id, 3)
        ->set("descriptions.{$compA->id}", 'Goed gepland.')
        ->call('submit')
        ->assertRedirect(route('student.evaluaties'));

    $evaluation = Evaluation::where('stage_id', $stage->id)
        ->where('evaluator_role', 'student')
        ->where('type', 'final')
        ->first();

    expect($evaluation)->not->toBeNull();
    expect($evaluation->status)->toBe('submitted');

    // Gewogen totaal: (5*60 + 3*40) / 100 = 4.2
    expect((float) $evaluation->overall_score)->toEqual(4.2);

    $scoreA = $evaluation->scores()->where('competency_id', $compA->id)->first();
    expect((int) $scoreA->score)->toBe(5);
    expect($scoreA->student_description)->toBe('Goed gepland.');
});

it('vereist een score voor elke competentie', function () {
    [$user, $stage, $compA] = makeStudentSelfEval();

    Livewire::actingAs($user)
        ->test(EvaluationForm::class, ['stage' => $stage, 'type' => 'final'])
        ->call('setScore', $compA->id, 4) // tweede competentie blijft leeg
        ->call('submit')
        ->assertHasErrors(['scores.*']);

    expect(Evaluation::where('stage_id', $stage->id)->where('evaluator_role', 'student')->exists())->toBeFalse();
});

it('weigert de zelfevaluatie van een andere student (403)', function () {
    [$user, $stage] = makeStudentSelfEval();

    $intruder = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $intruder->id]);

    $this->actingAs($intruder)
        ->get("/evaluaties/{$stage->id}/invullen/final")
        ->assertForbidden();
});
