<?php

use App\Livewire\Admin\FrameworkManager;
use App\Models\Competency;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

function makeFrameworkUser(string $roleName): User {
    return User::factory()->create([
        'role_id' => Role::where('name', $roleName)->value('id'),
    ]);
}

it('laat een admin het kaderbeheer openen', function () {
    $this->actingAs(makeFrameworkUser('admin'))
        ->get('/admin/framework')
        ->assertOk();
});

it('blokkeert een niet-admin met 403', function () {
    $this->actingAs(makeFrameworkUser('student'))
        ->get('/admin/framework')
        ->assertForbidden();
});

it('voegt een competentie toe aan het kader', function () {
    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->set('code', 'TST')
        ->set('title', 'Testcompetentie')
        ->set('weight', 25)
        ->call('addCompetency')
        ->assertHasNoErrors();

    expect(Competency::where('code', 'TST')->where('title', 'Testcompetentie')->exists())
        ->toBeTrue();
});

it('weigert een competentie zonder titel', function () {
    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->set('title', '')
        ->set('weight', 10)
        ->call('addCompetency')
        ->assertHasErrors(['title']);
});
