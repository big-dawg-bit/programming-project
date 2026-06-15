<?php

it('laat een docent het evaluatieformulier openen en blokkeert een student', function () {
    $this->seed();
    $stage = makeStageWithFramework();

    $this->actingAs(makeFrameworkUser('docent'))
        ->get("/stages/{$stage->id}/evaluatie")
        ->assertOk();

    $this->actingAs(makeFrameworkUser('student'))
        ->get("/stages/{$stage->id}/evaluatie")
        ->assertForbidden();
});
