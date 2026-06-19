<?php

use App\Livewire\Applications\ApplyForm;
use App\Models\Company;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

function studentVoorAanvraag(): User
{
    $user = User::factory()->withRole('student')->create();
    Student::factory()->create(['user_id' => $user->id]);

    return $user;
}

it('aanvaardt een geldig Belgisch btw-nummer en normaliseert het', function () {
    Livewire::actingAs(studentVoorAanvraag())->test(ApplyForm::class)
        ->set('new_company_name', 'Testbedrijf')
        ->set('new_company_vat', 'be 0123.456.789')
        ->call('addCompany')
        ->assertHasNoErrors();

    expect(Company::where('name', 'Testbedrijf')->first()->vat_number)->toBe('BE0123456789');
});

it('weigert een btw-nummer dat niet met BE + 10 cijfers begint', function () {
    Livewire::actingAs(studentVoorAanvraag())->test(ApplyForm::class)
        ->set('new_company_name', 'Foutbedrijf')
        ->set('new_company_vat', '0123456789')
        ->call('addCompany')
        ->assertHasErrors(['new_company_vat']);

    expect(Company::where('name', 'Foutbedrijf')->exists())->toBeFalse();
});

it('weigert BE met te weinig cijfers', function () {
    Livewire::actingAs(studentVoorAanvraag())->test(ApplyForm::class)
        ->set('new_company_name', 'Te Kort')
        ->set('new_company_vat', 'BE12345')
        ->call('addCompany')
        ->assertHasErrors(['new_company_vat']);
});

it('vereist een btw-nummer bij een nieuw bedrijf', function () {
    Livewire::actingAs(studentVoorAanvraag())->test(ApplyForm::class)
        ->set('new_company_name', 'Zonder Btw')
        ->set('new_company_vat', '')
        ->call('addCompany')
        ->assertHasErrors(['new_company_vat']);
});
