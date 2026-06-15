<?php

use App\Livewire\Applications\ApplyForm;
use App\Models\Company;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

it('laat een student een aanvraag indienen', function () {
    $student = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $student->id]);
    $company = Company::factory()->create();

    Livewire::actingAs($student)->test(ApplyForm::class)
        ->set('company_id', $company->id)
        ->set('position_title', 'Stagiair Netwerk')
        ->set('description', 'Beheer netwerkinfra')
        ->set('start_date', '2026-09-01')
        ->set('end_date', '2026-12-20')
        ->call('submit')
        ->assertHasNoErrors();

    expect(StageApplication::where('status', 'submitted')->count())->toBe(1);
});

it('weigert een ongeldige periode', function () {
    $student = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $student->id]);
    $company = Company::factory()->create();

    Livewire::actingAs($student)->test(ApplyForm::class)
        ->set('company_id', $company->id)
        ->set('position_title', 'Test')
        ->set('description', 'Test')
        ->set('start_date', '2026-12-20')
        ->set('end_date', '2026-09-01')
        ->call('submit')
        ->assertHasErrors(['end_date']);
});

it('weigert een docent op het aanvraagformulier', function () {
    $docent = User::factory()->withRole('docent')->create();
    $this->actingAs($docent)->get('/applications/create')->assertForbidden();
});
