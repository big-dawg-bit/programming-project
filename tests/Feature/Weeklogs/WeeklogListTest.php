<?php

use App\Livewire\Weeklogs\WeeklogList;
use App\Models\Company;
use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

/** Maakt een student met een actieve stage en geeft beide terug. */
function studentMetStage(): array
{
    $company = Company::factory()->create();
    $studentUser = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $studentUser->id]);

    $stage = Stage::create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'active',
    ]);

    return [$studentUser, $stage];
}

it('slaat een weeklog op met de drie opgesplitste velden', function () {
    [$studentUser, $stage] = studentMetStage();

    Livewire::actingAs($studentUser)
        ->test(WeeklogList::class)
        ->set('week_number', 1)
        ->set('tasksDescription', 'Ik heb de loginpagina gebouwd en getest.')
        ->set('reflection', 'Ik leerde werken met Livewire-componenten.')
        ->set('learningPoints', 'Validatie was eerst lastig.')
        ->call('save')
        ->assertHasNoErrors();

    $weeklog = $stage->weeklogs()->first();

    expect($weeklog)->not->toBeNull()
        ->and($weeklog->tasks_description)->toBe('Ik heb de loginpagina gebouwd en getest.')
        ->and($weeklog->reflection)->toBe('Ik leerde werken met Livewire-componenten.')
        ->and($weeklog->learning_points)->toBe('Validatie was eerst lastig.')
        // content blijft gevuld (samengevoegd) voor de docent/mentor-overzichten.
        ->and($weeklog->content)->toContain('Uitgevoerde taken:')
        ->and($weeklog->content)->toContain('Reflectie:');
});

it('vereist taken en reflectie, leerpunten zijn optioneel', function () {
    [$studentUser] = studentMetStage();

    Livewire::actingAs($studentUser)
        ->test(WeeklogList::class)
        ->set('week_number', 2)
        ->set('tasksDescription', '')
        ->set('reflection', '')
        ->set('learningPoints', '')
        ->call('save')
        ->assertHasErrors(['tasksDescription', 'reflection'])
        ->assertHasNoErrors(['learningPoints']);
});
