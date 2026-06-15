<?php

use App\Models\CompetencyFramework;
use App\Models\Stage;

it('seedt een actief competentiekader met gewichten die optellen tot 100', function () {
    $this->seed();

    $framework = CompetencyFramework::where('is_active', true)->first();

    expect($framework)->not->toBeNull();
    expect($framework->competencies()->count())->toBeGreaterThanOrEqual(4);
    expect((int) $framework->competencies()->sum('weight'))->toBe(100);
});

it('seedt minstens één stage die klaar is voor evaluatie', function () {
    $this->seed();

    $stage = Stage::whereNotNull('framework_id')->first();

    expect($stage)->not->toBeNull();
    expect($stage->framework)->not->toBeNull();
    expect($stage->framework->competencies()->count())->toBeGreaterThan(0);
});
