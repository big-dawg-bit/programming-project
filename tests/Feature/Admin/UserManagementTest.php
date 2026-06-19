<?php

use App\Livewire\Admin\UserManager;
use App\Models\Company;
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

it('toont een nieuw aangemaakte gebruiker meteen in de lijst, ook met paginatie', function () {
    // Genoeg gebruikers om paginatie te forceren (>15 per pagina).
    User::factory()->count(20)->create([
        'role_id' => Role::where('name', 'student')->value('id'),
    ]);

    Livewire::actingAs(makeUser('admin'))
        ->test(UserManager::class)
        // Naam die alfabetisch achteraan zou staan (= vroeger op de laatste pagina, onzichtbaar).
        ->set('name', 'Zzz Allerlaatste')
        ->set('email', 'zzz@student.ehb.be')
        ->set('selectedRole', 'student')
        ->call('createUser')
        ->assertHasNoErrors()
        ->assertSee('Zzz Allerlaatste'); // nieuwste-eerst + resetPage -> staat bovenaan pagina 1
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

it('maakt een student-subtype aan zodat de student in zijn portaal kan', function () {
    Livewire::actingAs(makeUser('admin'))
        ->test(UserManager::class)
        ->set('name', 'Nieuwe Student')
        ->set('email', 'nieuw@student.ehb.be')
        ->set('selectedRole', 'student')
        ->call('createUser')
        ->assertHasNoErrors();

    $user = User::where('email', 'nieuw@student.ehb.be')->first();

    expect($user->student)->not->toBeNull();
});

it('maakt een mentor-subtype aan en koppelt het bedrijf', function () {
    $company = Company::first();

    Livewire::actingAs(makeUser('admin'))
        ->test(UserManager::class)
        ->set('name', 'Nieuwe Mentor')
        ->set('email', 'nieuw@easi.net')
        ->set('selectedRole', 'mentor')
        ->set('companyId', $company->id)
        ->call('createUser')
        ->assertHasNoErrors();

    $user = User::where('email', 'nieuw@easi.net')->first();

    expect($user->mentor)->not->toBeNull()
        ->and($user->mentor->company_id)->toBe($company->id);
});
