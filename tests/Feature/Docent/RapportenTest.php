<?php

use App\Livewire\Docent\Eindbeoordeling;
use App\Livewire\Docent\Rapporten;
use App\Models\Stage;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

it('toont in Rapporten het cijfer en resultaat van de eindbeoordeling van de docent', function () {
    $stage = Stage::whereNotNull('docent_id')->whereNotNull('framework_id')->first();
    $docentUser = $stage->docent->user;

    // Zonder eindbeoordeling: nog te beoordelen, geen cijfer.
    Livewire::actingAs($docentUser)->test(Rapporten::class)
        ->assertSee('Nog te beoordelen');

    // Docent geeft de eindbeoordeling: elke competentie 16/20 -> geslaagd.
    $scores = $stage->framework->competencies->mapWithKeys(fn ($c) => [$c->id => 16])->all();
    $form = Livewire::actingAs($docentUser)->test(Eindbeoordeling::class, ['stage' => $stage]);
    foreach ($scores as $id => $val) {
        $form->set("scores.{$id}", $val);
    }
    $form->call('submit');

    // Rapporten toont nu het echte eindcijfer (16.0) en 'Geslaagd'.
    Livewire::actingAs($docentUser)->test(Rapporten::class)
        ->assertSee('16.0')
        ->assertSee('Geslaagd');
});
