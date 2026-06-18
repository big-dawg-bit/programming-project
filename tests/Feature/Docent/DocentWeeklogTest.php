<?php

use App\Livewire\Docent\Weeklogs as DocentWeeklogs;
use App\Models\Company;
use App\Models\Docent;
use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

/** Maakt een docent met een begeleide stage en geeft die stage terug. */
function docentMetStage(): array
{
    $company = Company::factory()->create();
    $docentUser = User::factory()->withRole('docent')->create();
    $docent = Docent::create(['user_id' => $docentUser->id, 'department' => 'Toegepaste Informatica']);

    $studentUser = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $studentUser->id]);

    $stage = Stage::create([
        'student_id' => $student->id,
        'docent_id' => $docent->id,
        'company_id' => $company->id,
        'status' => 'active',
    ]);

    return [$docentUser, $stage];
}

function ingediendeWeeklog(Stage $stage, int $week = 1)
{
    return $stage->weeklogs()->create([
        'week_number' => $week,
        'content' => 'Weeklog inhoud voor de test.',
        'status' => 'ingediend',
        'submitted_at' => now(),
    ]);
}

it('laat de docent reageren op een weeklog van een eigen student', function () {
    [$docentUser, $stage] = docentMetStage();
    $weeklog = ingediendeWeeklog($stage);

    Livewire::actingAs($docentUser)->test(DocentWeeklogs::class)
        ->call('toggleComments', $weeklog->id)
        ->set('newComment', 'Goed bezig, blijf zo doorgaan.')
        ->call('addComment', $weeklog->id)
        ->assertHasNoErrors();

    expect($weeklog->comments()->count())->toBe(1)
        ->and($weeklog->comments()->first()->author_id)->toBe($docentUser->id);
});

it('laat een docent niet reageren op een weeklog buiten zijn stages', function () {
    [$docentUser] = docentMetStage();

    // Weeklog van een andere docent/stage.
    [, $andereStage] = docentMetStage();
    $vreemd = ingediendeWeeklog($andereStage);

    Livewire::actingAs($docentUser)->test(DocentWeeklogs::class)
        ->set('newComment', 'Mag niet')
        ->call('addComment', $vreemd->id);
})->throws(ModelNotFoundException::class);
