<?php

use App\Livewire\Docent\Eindbeoordeling;
use App\Models\Docent;
use App\Models\Evaluation;
use App\Models\Stage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(); // rollen + framework + competenties + stage met toegewezen docent + mentor
    $this->stage = Stage::whereNotNull('framework_id')->first();
});

it('laat de toegewezen docent een bindende eindbeoordeling indienen', function () {
    $competencies = $this->stage->framework->competencies()->orderBy('sort_order')->get();

    $component = Livewire::actingAs($this->stage->docent->user)
        ->test(Eindbeoordeling::class, ['stage' => $this->stage]);

    foreach ($competencies as $competency) {
        $component->set("scores.{$competency->id}", 16);
    }

    $component->call('submit')->assertHasNoErrors();

    $evaluation = Evaluation::where('stage_id', $this->stage->id)
        ->where('evaluator_role', 'docent')
        ->where('type', 'final')
        ->first();

    expect($evaluation)->not->toBeNull()
        ->and($evaluation->status)->toBe('submitted')
        ->and((float) $evaluation->overall_score)->toBe(16.0)
        ->and($this->stage->fresh()->final_evaluation_id)->toBe($evaluation->id);
});

it('vereist een definitieve score voor elke competentie', function () {
    Livewire::actingAs($this->stage->docent->user)
        ->test(Eindbeoordeling::class, ['stage' => $this->stage])
        ->call('submit')
        ->assertHasErrors(['scores.*']);

    expect(Evaluation::where('stage_id', $this->stage->id)->where('evaluator_role', 'docent')->exists())->toBeFalse();
});

it('blokkeert een docent die niet aan de stage is toegewezen (403)', function () {
    $andereUser = User::factory()->withRole('docent')->create();
    Docent::create(['user_id' => $andereUser->id, 'department' => 'TI']);

    $this->actingAs($andereUser)
        ->get(route('docent.eindbeoordeling', $this->stage))
        ->assertForbidden();
});
