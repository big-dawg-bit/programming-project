<?php

use App\Livewire\Evaluations\EvaluationForm;
use App\Models\Competency;
use App\Models\CompetencyFramework;
use App\Models\Docent;
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

    // Toegewezen docent: submit() leest de ingelogde gebruiker (evaluator_role),
    // en de scoping in mount() laat enkel de eigen docent/mentor toe.
    $docentUser = User::factory()->withRole('docent')->create();
    $docent = Docent::create([
        'user_id' => $docentUser->id,
        'department' => 'Toegepaste Informatica',
    ]);

    return Stage::create([
        'student_id' => $student->id,
        'docent_id' => $docent->id,
        'framework_id' => $framework->id,
        'status' => 'active',
    ]);
}

it('rendert een veld per competentie van het framework', function () {
    $stage = makeStageWithFramework();

    Livewire::actingAs($stage->docent->user)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->assertSee('Competentie A')
        ->assertSee('Competentie B');
});

it('toont een extra veld wanneer een competentie wordt toegevoegd', function () {
    $stage = makeStageWithFramework();
    $docentUser = $stage->docent->user;

    Livewire::actingAs($docentUser)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->assertSee('Competentie A')
        ->assertSee('Competentie B')
        ->assertDontSee('Competentie C');

    Competency::create([
        'framework_id' => $stage->framework_id,
        'code' => 'C',
        'title' => 'Competentie C',
        'weight' => 0,
    ]);

    Livewire::actingAs($docentUser)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->assertSee('Competentie C');
});

it('legt het gewicht vast bij indienen en blijft onveranderd als het gewicht later wijzigt', function () {
    $stage = makeStageWithFramework();
    $competentieA = Competency::where('code', 'A')->first();

    Livewire::actingAs($stage->docent->user)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->set("scores.{$competentieA->id}", 16)
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

    Livewire::actingAs($stage->docent->user)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->set("scores.{$a->id}", 16)
        ->set("scores.{$b->id}", 12)
        ->call('submit');

    $evaluation = Evaluation::first();

    // (16*50 + 12*50) / 100 = 14
    expect((float) $evaluation->overall_score)->toBe(14.0);
});

it('ondersteunt een mid-term en final evaluatie onafhankelijk op dezelfde stage', function () {
    $stage = makeStageWithFramework();
    $docentUser = $stage->docent->user;
    $a = Competency::where('code', 'A')->first();
    $b = Competency::where('code', 'B')->first();

    // mid-term: 16 en 12 -> 14
    Livewire::actingAs($docentUser)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->set('type', 'mid-term')
        ->set("scores.{$a->id}", 16)
        ->set("scores.{$b->id}", 12)
        ->call('submit');

    // final: 18 en 20 -> 19
    Livewire::actingAs($docentUser)
        ->test(EvaluationForm::class, ['stage' => $stage])
        ->set('type', 'final')
        ->set("scores.{$a->id}", 18)
        ->set("scores.{$b->id}", 20)
        ->call('submit');

    expect(Evaluation::where('stage_id', $stage->id)->count())->toBe(2);

    $midterm = Evaluation::where('stage_id', $stage->id)->where('type', 'mid-term')->first();
    $final = Evaluation::where('stage_id', $stage->id)->where('type', 'final')->first();

    expect((float) $midterm->overall_score)->toBe(14.0)
        ->and((float) $final->overall_score)->toBe(19.0);
});
