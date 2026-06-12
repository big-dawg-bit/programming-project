<?php

use App\Livewire\Evaluations\EvaluationForm;
use App\Models\Competency;
use App\Models\CompetencyFramework;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

function makeStageWithFramework(): Stage {
    $framework = CompetencyFramework::create([
        'name' => 'Testkader',
        'version' => 1,
        'is_active' => true,
    ]);

    foreach (['A' => 50, 'B' => 50] as $code => $weight) {
        Competency::create([
            'framework_id' => $framework->id,
            'code' => $code,
            'title' => "Competentie {$code}",
            'weight' => $weight,
        ]);
    }

    $user = User::factory()->create();

    $student = Student::create([
        'user_id' => $user->id,
        'student_number' => 'r0123456',
        'study_program' => 'Toegepaste Informatica',
    ]);

    return Stage::create([
        'student_id' => $student->id,
        'framework_id' => $framework->id,
        'status' => 'active',
    ]);
}

it('rendert een veld per competentie van het framework', function () {
    $stage = makeStageWithFramework();

    Livewire::test(EvaluationForm::class, ['stage' => $stage])
        ->assertSee('Competentie A')
        ->assertSee('Competentie B');
});

it('toont een extra veld wanneer een competentie wordt toegevoegd', function () {
    $stage = makeStageWithFramework();

    Livewire::test(EvaluationForm::class, ['stage' => $stage])
        ->assertSee('Competentie A')
        ->assertSee('Competentie B')
        ->assertDontSee('Competentie C');

    Competency::create([
        'framework_id' => $stage->framework_id,
        'code' => 'C',
        'title' => 'Competentie C',
        'weight' => 0,
    ]);

    Livewire::test(EvaluationForm::class, ['stage' => $stage])
        ->assertSee('Competentie C');
});

it('legt het gewicht vast bij indienen en blijft onveranderd als het gewicht later wijzigt', function () {
    $stage = makeStageWithFramework();
    $competentieA = Competency::where('code', 'A')->first();

    Livewire::test(EvaluationForm::class, ['stage' => $stage])
        ->set("scores.{$competentieA->id}", 80)
        ->call('submit');

    $score = EvaluationScore::where('competency_id', $competentieA->id)->first();

    expect($score->weight_snapshot)->toBe(50);

    $competentieA->update(['weight' => 90]);

    expect($score->fresh()->weight_snapshot)->toBe(50);
});

it('berekent het gewogen eindcijfer uit de snapshots', function () {
    $stage = makeStageWithFramework();
    $a = Competency::where('code', 'A')->first();
    $b = Competency::where('code', 'B')->first();

    Livewire::test(EvaluationForm::class, ['stage' => $stage])
        ->set("scores.{$a->id}", 80)
        ->set("scores.{$b->id}", 60)
        ->call('submit');

    $evaluation = Evaluation::first();

    // (80*50 + 60*50) / 100 = 70
    expect((float) $evaluation->overall_score)->toBe(70.0);
});
