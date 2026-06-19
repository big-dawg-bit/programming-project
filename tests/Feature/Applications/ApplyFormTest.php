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
        ->assertHasNoErrors()
        ->assertRedirect(route('student.stage'));

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

it('laat een student een nieuw bedrijf toevoegen en koppelt het', function () {
    $student = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $student->id]);

    $component = Livewire::actingAs($student)->test(ApplyForm::class)
        ->set('new_company_name', 'Just Russel')
        ->set('new_company_address', 'Antwerpen')
        ->set('new_company_vat', 'BE0123456789')
        ->set('new_company_email', 'hr@justrussel.com')
        ->call('addCompany')
        ->assertHasNoErrors();

    $company = Company::where('name', 'Just Russel')->first();

    expect($company)->not->toBeNull()
        ->and($company->vat_number)->toBe('BE0123456789')
        ->and($component->get('company_id'))->toBe($company->id);
});

it('zet de status terug op ingediend na opnieuw indienen', function () {
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $company = Company::factory()->create();

    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'changes_requested',
    ]);

    Livewire::actingAs($user)->test(ApplyForm::class, ['application' => $application])
        ->set('position_title', 'Aangepaste titel')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('student.stage'));

    $application->refresh();

    expect($application->status)->toBe('submitted')
        ->and($application->position_title)->toBe('Aangepaste titel')
        ->and($application->submitted_at)->not->toBeNull();
});

it('blokkeert het bewerken van andermans aanvraag', function () {
    $user = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $user->id]);

    // aanvraag van een andere student
    $other = Student::factory()->create();
    $application = StageApplication::factory()->create([
        'student_id' => $other->id,
        'status' => 'changes_requested',
    ]);

    $this->actingAs($user)->get("/applications/{$application->id}/edit")->assertForbidden();
});

it('blokkeert opnieuw indienen wanneer geen aanpassingen gevraagd zijn', function () {
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'status' => 'submitted',
    ]);

    $this->actingAs($user)->get("/applications/{$application->id}/edit")->assertForbidden();
});
