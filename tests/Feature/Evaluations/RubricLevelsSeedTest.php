<?php

use App\Models\Competency;

it('seedt niveau-omschrijvingen op de rubriek-competenties', function () {
    $this->seed();

    $comp = Competency::where('code', 'D1')->first();

    expect($comp)->not->toBeNull()
        ->and($comp->level_full)->not->toBeEmpty()
        ->and($comp->level_good)->not->toBeEmpty()
        ->and($comp->level_low)->not->toBeEmpty();
});
