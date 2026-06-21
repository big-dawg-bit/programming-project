<?php

use App\Models\Stage;
use App\Models\User;

beforeEach(fn () => $this->seed());

it('laat de toegewezen docent de eindbeoordeling openen via de echte route', function () {
    $stage = Stage::whereNotNull('docent_id')->whereNotNull('framework_id')->first();
    $docentUser = $stage->docent->user;

    // Vroeger zat deze route in de role:student-groep -> de docent kreeg 403.
    $this->actingAs($docentUser)
        ->get(route('docent.eindbeoordeling', $stage))
        ->assertOk();
});

it('blokkeert een student op de eindbeoordeling-route', function () {
    $stage = Stage::whereNotNull('docent_id')->first();
    $student = User::where('email', 'student@ehb.be')->first();

    $this->actingAs($student)
        ->get(route('docent.eindbeoordeling', $stage))
        ->assertForbidden();
});
