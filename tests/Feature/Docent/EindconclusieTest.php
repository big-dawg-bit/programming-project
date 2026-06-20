<?php

use App\Livewire\Docent\Eindbeoordeling;
use App\Models\Evaluation;
use App\Models\Stage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed();
    $this->stage = Stage::whereNotNull('framework_id')->first();
    $this->competencies = $this->stage->framework->competencies()->orderBy('sort_order')->get();
});

it('bewaart de conclusie en zet het resultaat op geslaagd bij een voldoende', function () {
    $component = Livewire::actingAs($this->stage->docent->user)
        ->test(Eindbeoordeling::class, ['stage' => $this->stage]);

    foreach ($this->competencies as $competency) {
        $component->set("scores.{$competency->id}", 16);
    }

    $component->set('conclusion', 'Sterke stage, zelfstandig gewerkt.')
        ->call('submit')
        ->assertHasNoErrors();

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'docent')->where('type', 'final')->first();

    expect($evaluation->conclusion)->toBe('Sterke stage, zelfstandig gewerkt.')
        ->and($evaluation->result)->toBe('geslaagd');
});

it('zet het resultaat op niet_geslaagd bij een onvoldoende', function () {
    $component = Livewire::actingAs($this->stage->docent->user)
        ->test(Eindbeoordeling::class, ['stage' => $this->stage]);

    foreach ($this->competencies as $competency) {
        $component->set("scores.{$competency->id}", 8);
    }

    $component->call('submit')->assertHasNoErrors();

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'docent')->where('type', 'final')->first();

    expect((float) $evaluation->overall_score)->toBe(8.0)
        ->and($evaluation->result)->toBe('niet_geslaagd');
});

it('toont het live totaal op /100 en de geslaagd-badge', function () {
    $component = Livewire::actingAs($this->stage->docent->user)
        ->test(Eindbeoordeling::class, ['stage' => $this->stage]);

    foreach ($this->competencies as $competency) {
        $component->set("scores.{$competency->id}", 14);
    }

    // 14/20 gewogen → 70/100, geslaagd
    $component->assertSee('70')->assertSee('Geslaagd');
});
