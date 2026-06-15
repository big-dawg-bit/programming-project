<?php

use App\Livewire\Applications\ApplyForm;
use App\Models\Company;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

it('laat een student een nieuw bedrijf toevoegen en koppelt het aan de aanvraag', function () {
    $student = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $student->id]);

    Livewire::actingAs($student)->test(ApplyForm::class)
        ->set('newCompany', true)
        ->set('company_name', 'Nieuwe BV')
        ->set('company_address', 'Teststraat 1, Brussel')
        ->set('company_vat_number', 'BE0123456789')
        ->set('company_contact_email', 'contact@nieuwe.be')
        ->set('position_title', 'Stagiair Netwerk')
        ->set('description', 'Beheer netwerkinfra')
        ->set('start_date', '2026-09-01')
        ->set('end_date', '2026-12-20')
        ->call('submit')
        ->assertHasNoErrors();

    $company = Company::where('name', 'Nieuwe BV')->first();
    expect($company)->not->toBeNull();
    expect($company->vat_number)->toBe('BE0123456789');

    $application = StageApplication::where('status', 'submitted')->first();
    expect($application->company_id)->toBe($company->id);
});
