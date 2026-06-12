<?php

use App\Models\Competency;
use App\Models\CompetencyFramework;

/*
 | Test de Eloquent-relaties en de foreign-key cascades.
 | Gebruikt create() rechtstreeks, dus geen factories nodig.
 | Plaats in: tests/Feature/Database/RelationshipsTest.php
 */

function maakFramework(): CompetencyFramework
{
    return CompetencyFramework::create([
        'name' => 'Toegepaste Informatica 2025',
        'study_program' => 'Toegepaste Informatica',
        'version' => 1,
        'is_active' => true,
    ]);
}

it('koppelt competenties aan hun framework (hasMany / belongsTo)', function () {
    $framework = maakFramework();

    $framework->competencies()->createMany([
        ['title' => 'Communicatie', 'weight' => 40, 'sort_order' => 1],
        ['title' => 'Technische kennis', 'weight' => 60, 'sort_order' => 2],
    ]);

    expect($framework->competencies)->toHaveCount(2);

    $eerste = Competency::where('title', 'Communicatie')->first();
    expect($eerste->framework->id)->toBe($framework->id);
});

it('verwijdert competenties mee als het framework verdwijnt (cascadeOnDelete)', function () {
    $framework = maakFramework();
    $framework->competencies()->create(['title' => 'Samenwerken', 'weight' => 100]);

    expect(Competency::count())->toBe(1);

    $framework->delete();

    expect(Competency::count())->toBe(0);
});

it('weigert een competentie zonder geldig framework (FK-constraint)', function () {
    Competency::create([
        'framework_id' => 999999, // bestaat niet
        'title' => 'Ongeldig',
        'weight' => 10,
    ]);
})->throws(Illuminate\Database\QueryException::class);
