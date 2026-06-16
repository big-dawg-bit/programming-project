<?php

use App\Models\CompetencyFramework;
use Database\Seeders\CompetencyFrameworkSeeder;

it('seedt een framework met competenties die optellen tot 100', function () {
    $this->seed(CompetencyFrameworkSeeder::class);

    $framework = CompetencyFramework::first();

    expect($framework)->not->toBeNull()
        ->and($framework->competencies)->toHaveCount(5)
        ->and($framework->competencies->sum('weight'))->toBe(100);
});
