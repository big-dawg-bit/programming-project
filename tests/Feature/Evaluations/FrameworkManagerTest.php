<?php

use App\Livewire\Admin\FrameworkManager;
use App\Models\Competency;
use App\Models\CompetencyFramework;
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

it('bewerkt de inhoud van een bestaande competentie', function () {
    $competency = Competency::query()->first();

    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->call('startEdit', $competency->id)
        ->set('editTitle', 'Aangepaste titel')
        ->set('editCode', 'EDIT')
        ->set('editDescription', 'Nieuwe omschrijving')
        ->set('editWeight', 30)
        ->call('saveEdit')
        ->assertHasNoErrors();

    $competency->refresh();

    expect($competency->title)->toBe('Aangepaste titel')
        ->and($competency->code)->toBe('EDIT')
        ->and($competency->description)->toBe('Nieuwe omschrijving')
        ->and((int) $competency->weight)->toBe(30);
});

it('weigert een bewerking zonder titel', function () {
    $competency = Competency::query()->first();

    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->call('startEdit', $competency->id)
        ->set('editTitle', '')
        ->call('saveEdit')
        ->assertHasErrors(['editTitle']);
});

it('maakt een nieuwe versie als kopie van het actieve kader', function () {
    $actiefAantal = CompetencyFramework::where('is_active', true)->first()->competencies()->count();

    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->call('createVersion');

    $nieuw = CompetencyFramework::orderByDesc('version')->first();

    expect(CompetencyFramework::count())->toBe(2)
        ->and($nieuw->version)->toBe(2)
        ->and((bool) $nieuw->is_active)->toBeFalse()
        ->and($nieuw->competencies()->count())->toBe($actiefAantal);
});

it('activeert exact één versie tegelijk', function () {
    $oud = CompetencyFramework::where('is_active', true)->first();
    $nieuw = CompetencyFramework::create(['name' => 'Kader v2', 'version' => 2, 'is_active' => false]);
    $nieuw->competencies()->create(['title' => 'Comp', 'weight' => 100, 'sort_order' => 1]);

    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->call('activate', $nieuw->id);

    expect((bool) $nieuw->fresh()->is_active)->toBeTrue()
        ->and((bool) $oud->fresh()->is_active)->toBeFalse()
        ->and(CompetencyFramework::where('is_active', true)->count())->toBe(1);
});

it('weigert een versie te activeren als de gewichten niet op 100 staan', function () {
    $nieuw = CompetencyFramework::create(['name' => 'Onvolledig kader', 'version' => 2, 'is_active' => false]);
    $nieuw->competencies()->create(['title' => 'Comp', 'weight' => 40, 'sort_order' => 1]);

    Livewire::actingAs(makeFrameworkUser('admin'))
        ->test(FrameworkManager::class)
        ->call('activate', $nieuw->id);

    expect((bool) $nieuw->fresh()->is_active)->toBeFalse();
});
