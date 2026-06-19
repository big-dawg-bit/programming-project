<?php

use App\Livewire\Applications\ReviewQueue;
use App\Models\Company;
use App\Models\CompetencyFramework;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Stage;
use App\Models\StageApplication;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(); // rollen + bedrijf (Easi) + docent + mentor + framework
    $this->commissie = User::factory()->withRole('stagecommissie')->create();
});

it('zet docent, mentor en framework op de stage bij goedkeuring', function () {
    // Aanvraag op het geseeded bedrijf zodat de geseeded mentor bij dit bedrijf hoort.
    $application = StageApplication::factory()->create([
        'company_id' => Company::first()->id,
        'status' => 'submitted',
    ]);

    $docent = Docent::first();
    $mentor = Mentor::first();
    $framework = CompetencyFramework::first();

    Livewire::actingAs($this->commissie)
        ->test(ReviewQueue::class)
        ->set("docentId.{$application->id}", $docent->id)
        ->set("mentorId.{$application->id}", $mentor->id)
        ->set("frameworkId.{$application->id}", $framework->id)
        ->call('approve', $application->id)
        ->assertHasNoErrors();

    $stage = Stage::where('application_id', $application->id)->first();

    expect($stage)->not->toBeNull();
    expect($stage->docent_id)->toBe($docent->id);
    expect($stage->mentor_id)->toBe($mentor->id);
    expect($stage->framework_id)->toBe($framework->id);
    expect($application->fresh()->status)->toBe('approved');
});

it('laat een mentor van een ander bedrijf toekennen aan de aanvraag', function () {
    // Aanvraag bij een nieuw bedrijf dat zelf geen mentor heeft.
    $anderBedrijf = Company::factory()->create();
    $application = StageApplication::factory()->create([
        'company_id' => $anderBedrijf->id,
        'status' => 'submitted',
    ]);

    // De geseeded mentor hoort bij Easi, maar moet tóch toewijsbaar zijn.
    $mentor = Mentor::first();
    $docent = Docent::first();
    $framework = CompetencyFramework::first();

    Livewire::actingAs($this->commissie)
        ->test(ReviewQueue::class)
        ->set("docentId.{$application->id}", $docent->id)
        ->set("mentorId.{$application->id}", $mentor->id)
        ->set("frameworkId.{$application->id}", $framework->id)
        ->call('approve', $application->id)
        ->assertHasNoErrors();

    $stage = Stage::where('application_id', $application->id)->first();

    expect($stage)->not->toBeNull()
        ->and($stage->mentor_id)->toBe($mentor->id)
        ->and($application->fresh()->status)->toBe('approved');
});

it('keurt niet goed en maakt geen stage zonder docent, mentor en framework', function () {
    $application = StageApplication::factory()->create(['status' => 'submitted']);

    Livewire::actingAs($this->commissie)
        ->test(ReviewQueue::class)
        ->call('approve', $application->id)
        ->assertHasErrors([
            "docentId.{$application->id}",
            "mentorId.{$application->id}",
            "frameworkId.{$application->id}",
        ]);

    expect(Stage::where('application_id', $application->id)->exists())->toBeFalse();
    expect($application->fresh()->status)->toBe('submitted');
});
