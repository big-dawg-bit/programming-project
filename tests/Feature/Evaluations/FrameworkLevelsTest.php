<?php

use App\Livewire\Admin\FrameworkManager;
use App\Models\Competency;
use App\Models\User;
use Livewire\Livewire;

it('laat een admin de rubriek-niveaus van een competentie bewerken', function () {
    $this->seed();

    $admin = User::factory()->withRole('admin')->create();
    $competency = Competency::where('code', 'D1')->first();

    Livewire::actingAs($admin)
        ->test(FrameworkManager::class)
        ->call('startEdit', $competency->id)
        ->set('editLevelFull', 'Aangepaste volledig-tekst.')
        ->set('editLevelGood', 'Aangepaste goed-tekst.')
        ->set('editLevelLow', 'Aangepaste onvoldoende-tekst.')
        ->call('saveEdit')
        ->assertHasNoErrors();

    $competency->refresh();

    expect($competency->level_full)->toBe('Aangepaste volledig-tekst.')
        ->and($competency->level_good)->toBe('Aangepaste goed-tekst.')
        ->and($competency->level_low)->toBe('Aangepaste onvoldoende-tekst.');
});
