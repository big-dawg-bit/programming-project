<?php

it('laat de toegewezen docent het formulier openen en blokkeert anderen', function () {
    $this->seed();
    $stage = makeStageWithFramework();

    // De toegewezen docent van deze stage mag het formulier openen.
    $this->actingAs($stage->docent->user)
        ->get("/stages/{$stage->id}/evaluatie")
        ->assertOk();

    // Een andere docent (niet toegewezen aan deze stage) wordt geweigerd (scoping in mount()).
    $this->actingAs(makeFrameworkUser('docent'))
        ->get("/stages/{$stage->id}/evaluatie")
        ->assertForbidden();

    // Een student wordt al door de rol-middleware geweigerd.
    $this->actingAs(makeFrameworkUser('student'))
        ->get("/stages/{$stage->id}/evaluatie")
        ->assertForbidden();
});
