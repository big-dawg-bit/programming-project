<?php

use App\Livewire\Applications\ReviewQueue;
use App\Models\Company;
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
    StageApplication::factory()->create(['status' => 'submitted', 'company_status' => 'accepted', 'position_title' => 'WEL']);
    StageApplication::factory()->create(['status' => 'approved', 'position_title' => 'NIET']);

    Livewire::actingAs($lid)->test(ReviewQueue::class)
        ->assertSee('WEL')
        ->assertDontSee('NIET');
});

it('keurt een aanvraag goed en maakt een stage aan', function () {
    $this->seed();

    $lid = User::factory()->withRole('stagecommissie')->create();
    $application = StageApplication::factory()->create([
        'company_id' => Company::first()->id,
        'status' => 'submitted',
        'company_status' => 'accepted',
    ]);

    Livewire::actingAs($lid)->test(ReviewQueue::class)
        ->call('approve', $application->id);

    $application->refresh();

    expect($application->status)->toBe('approved')
        ->and($application->reviews()->count())->toBe(1)
        ->and($application->reviews()->first()->reviewer_id)->toBe($lid->id)
        ->and($application->stage()->count())->toBe(1);
});

it('wijst een aanvraag af met feedback', function () {
    $lid = User::factory()->withRole('stagecommissie')->create();
    $application = StageApplication::factory()->create(['status' => 'submitted']);

    Livewire::actingAs($lid)->test(ReviewQueue::class)
        ->set("feedback.$application->id", 'Bedrijf niet erkend')
        ->call('reject', $application->id);

    $application->refresh();

    expect($application->status)->toBe('rejected')
        ->and($application->reviews()->first()->decision)->toBe('rejected')
        ->and($application->reviews()->first()->feedback)->toBe('Bedrijf niet erkend')
        ->and($application->stage()->count())->toBe(0);
});

it('zet een aanvraag op aanpassingen vereist en bewaart feedback', function () {
    $lid = User::factory()->withRole('stagecommissie')->create();
    $application = StageApplication::factory()->create(['status' => 'submitted']);

    Livewire::actingAs($lid)->test(ReviewQueue::class)
        ->set("feedback.{$application->id}", 'Periode aanpassen aub')
        ->call('requestChanges', $application->id);

    $application->refresh();

    expect($application->status)->toBe('changes_requested');
    expect($application->reviews()->first()->decision)->toBe('changes_requested');
    expect($application->reviews()->first()->feedback)->toBe('Periode aanpassen aub');
    expect($application->stage()->count())->toBe(0);
});
