<?php

use App\Livewire\Admin\FrameworkManager;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

function makeAuditUser(string $roleName): User
{
    return User::factory()->create([
        'role_id' => Role::where('name', $roleName)->value('id'),
    ]);
}

it('logt een admin-actie in het audit log', function () {
    Livewire::actingAs(makeAuditUser('admin'))
        ->test(FrameworkManager::class)
        ->set('title', 'Gelogde competentie')
        ->set('weight', 10)
        ->call('addCompetency')
        ->assertHasNoErrors();

    expect(AuditLog::where('action', 'like', '%Gelogde competentie%')->exists())->toBeTrue();
});

it('laat een admin het logboek openen', function () {
    $this->actingAs(makeAuditUser('admin'))
        ->get('/admin/logboek')
        ->assertOk();
});

it('blokkeert een niet-admin op het logboek', function () {
    $this->actingAs(makeAuditUser('student'))
        ->get('/admin/logboek')
        ->assertForbidden();
});
