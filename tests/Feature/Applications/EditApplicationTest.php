<?php

use App\Livewire\Applications\EditApplication;
use App\Models\Company;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

it('laat de eigenaar aanpassen en opnieuw indienen', function () {
    $user = User::factory()->withRole('student')->create();
    $student = Student::create(['user_id' => $user->id]);
    $company = Company::factory()->create();
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'status' => 'changes_requested',
    ]);

    Livewire::actingAs($user)->test(EditApplication::class, ['application' => $application])
        ->set('company_id', $company->id)
        ->set('position_title', 'Aangepaste functie')
        ->set('description', 'Aangepaste omschrijving')
        ->set('start_date', '2026-09-01')
        ->set('end_date', '2026-12-20')
        ->call('resubmit');

    $application->refresh();

    expect($application->status)->toBe('submitted');
    expect($application->position_title)->toBe('Aangepaste functie');
});

it('blokkeert een andere student (403)', function () {
    $owner = User::factory()->withRole('student')->create();
    $ownerStudent = Student::create(['user_id' => $owner->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $ownerStudent->id,
        'status' => 'changes_requested',
    ]);

    $other = User::factory()->withRole('student')->create();
    Student::create(['user_id' => $other->id]);

    $this->actingAs($other)->get(route('applications.edit', $application))->assertForbidden();
});

it('blokkeert bewerken als de status niet changes_requested is', function () {
    $user = User::factory()->withRole('student')->create();
    $student = Student::create(['user_id' => $user->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'status' => 'submitted',
    ]);

    $this->actingAs($user)->get(route('applications.edit', $application))->assertForbidden();
});
