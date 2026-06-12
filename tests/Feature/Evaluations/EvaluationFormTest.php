<?php

use App\Livewire\Evaluations\EvaluationForm;
use App\Models\Competency;
use App\Models\CompetencyFramework;
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
