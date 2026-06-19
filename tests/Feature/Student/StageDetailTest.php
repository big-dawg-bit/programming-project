<?php

use App\Livewire\Student\StageApplicationDetail;
use App\Models\Company;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

function studentMetAanvraag(string $status = 'approved'): array
{
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => Company::factory()->create()->id,
        'status' => $status,
    ]);

    return [$user, $application];
}

it('laat een student het eigen aanvraagdetail zien', function () {
    [$user, $application] = studentMetAanvraag();

    $this->actingAs($user)
        ->get(route('student.stage.show', $application))
        ->assertOk()
        ->assertSee('Checklist');
});

it('blokkeert het aanvraagdetail van een andere student', function () {
    [$user] = studentMetAanvraag();
    [, $aanvraagVanAnder] = studentMetAanvraag();

    $this->actingAs($user)
        ->get(route('student.stage.show', $aanvraagVanAnder))
        ->assertForbidden();
});

it('toont alle aanvragen van de student op het overzicht', function () {
    [$user, $application] = studentMetAanvraag();

    $this->actingAs($user)
        ->get(route('student.stage'))
        ->assertOk()
        ->assertSee($application->company->name)
        ->assertSee('Stage aanvragen');
});

it('laat een student een ingediende aanvraag annuleren', function () {
    [$user, $application] = studentMetAanvraag('submitted');

    Livewire::actingAs($user)
        ->test(StageApplicationDetail::class, ['application' => $application])
        ->call('annuleer')
        ->assertRedirect(route('student.stage'));

    expect(StageApplication::find($application->id))->toBeNull();
});

it('laat een goedgekeurde aanvraag niet annuleren', function () {
    [$user, $application] = studentMetAanvraag('approved');

    Livewire::actingAs($user)
        ->test(StageApplicationDetail::class, ['application' => $application])
        ->call('annuleer');

    expect(StageApplication::find($application->id))->not->toBeNull();
});
