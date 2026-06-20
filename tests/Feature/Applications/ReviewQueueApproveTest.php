<?php

use App\Livewire\Applications\ReviewQueue;
use App\Models\Company;
use App\Models\Stage;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed();
    $this->commissie = User::factory()->withRole('stagecommissie')->create();
});

it('keurt goed en maakt automatisch stage, framework en overeenkomst aan', function () {
    $application = StageApplication::factory()->create([
        'company_id' => Company::first()->id,
        'status' => 'submitted',
        'company_status' => 'accepted',
    ]);

    Livewire::actingAs($this->commissie)
        ->test(ReviewQueue::class)
        ->call('approve', $application->id)
        ->assertHasNoErrors();

    $stage = Stage::where('application_id', $application->id)->first();

    expect($stage)->not->toBeNull()
        ->and($stage->framework_id)->not->toBeNull()
        ->and($application->fresh()->status)->toBe('approved');

    $agreement = StageAgreement::where('application_id', $application->id)->first();
    expect($agreement)->not->toBeNull()
        ->and($agreement->status)->toBe('te_ondertekenen');
});
