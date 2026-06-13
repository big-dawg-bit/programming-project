<?php

use App\Livewire\Applications\ReviewQueue;
use App\Models\StageApplication;
use App\Models\User;
use Livewire\Livewire;

it('toont de wachtrij aan een commissielid', function () {
    $lid = User::factory()->withRole('stagecommissie')->create();
    StageApplication::factory()->create(['status' => 'submitted']);

    $this->actingAs($lid)->get('/applications/review')->assertOk();
});

it('blokkeert een student op de wachtrij', function () {
    $student = User::factory()->withRole('student')->create();

    $this->actingAs($student)->get('/applications/review')->assertForbidden();
});

it('toont enkel ingediende aanvragen', function () {
    $lid = User::factory()->withRole('stagecommissie')->create();
    StageApplication::factory()->create(['status' => 'submitted', 'position_title' => 'WEL']);
    StageApplication::factory()->create(['status' => 'approved', 'position_title' => 'NIET']);

    Livewire::actingAs($lid)->test(ReviewQueue::class)
        ->assertSee('WEL')
        ->assertDontSee('NIET');
});
