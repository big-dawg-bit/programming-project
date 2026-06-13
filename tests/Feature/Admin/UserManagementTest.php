<?php

use App\Livewire\Admin\UserManager;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

function makeUser(string $roleName): User
{
    return User::factory()->create([
        'role_id' => Role::where('name', $roleName)->value('id'),
    ]);
}

it('laat een admin de gebruikerslijst openen', function () {
    $this->actingAs(makeUser('admin'))->get('/admin/users')->assertOk();
});

it('blokkeert een niet-admin met 403', function () {
    $this->actingAs(makeUser('student'))->get('/admin/users')->assertForbidden();
});

it('maakt een nieuwe gebruiker met rol aan', function () {
    Livewire::actingAs(makeUser('admin'))
        ->test(UserManager::class)
        ->set('name', 'Test Student')
        ->set('email', 'test@student.ehb.be')
        ->set('selectedRole', 'student')
        ->call('createUser')
        ->assertHasNoErrors();

    expect(User::where('email', 'test@student.ehb.be')->exists())->toBeTrue();
});

it('wijzigt de rol van een gebruiker', function () {
    $user = makeUser('student');
    $docentId = Role::where('name', 'docent')->value('id');

    Livewire::actingAs(makeUser('admin'))
        ->test(UserManager::class)
        ->call('changeRole', $user->id, $docentId);

    expect($user->fresh()->role_id)->toBe($docentId);
});

it('zet een gebruiker op inactief en terug', function () {
    $user = makeUser('student');
    $cmp = Livewire::actingAs(makeUser('admin'))
        ->test(UserManager::class);

    $cmp->call('toggleActive', $user->id);
    expect($user->fresh()->is_active)->toBeFalse();

    $cmp->call('toggleActive', $user->id);
    expect($user->fresh()->is_active)->toBeTrue();
});
