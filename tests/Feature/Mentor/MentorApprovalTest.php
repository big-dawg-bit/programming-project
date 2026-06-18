<?php

use App\Livewire\Mentor\Dashboard as MentorDashboard;
use App\Models\Company;
use App\Models\Mentor;
use App\Models\Stage;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

/** Maakt een mentor met een toegewezen stagiair (goedgekeurde aanvraag + actieve stage). */
function mentorMetStagiair(): array
{
    $company = Company::factory()->create();

    $mentorUser = User::factory()->withRole('mentor')->create();
    $mentor = Mentor::create(['user_id' => $mentorUser->id, 'company_id' => $company->id]);

    $studentUser = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $studentUser->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'approved',
    ]);
    $stage = Stage::create([
        'student_id' => $student->id,
        'mentor_id' => $mentor->id,
        'company_id' => $company->id,
        'application_id' => $application->id,
        'status' => 'active',
    ]);

    return [$mentorUser, $stage, $application];
}

it('laat de mentor akkoord geven, wat de overeenkomst aanmaakt', function () {
    [$mentorUser, $stage, $application] = mentorMetStagiair();

    Livewire::actingAs($mentorUser)->test(MentorDashboard::class)
        ->call('approve', $stage->id)
        ->assertHasNoErrors();

    $agreement = StageAgreement::where('application_id', $application->id)->first();

    expect($agreement)->not->toBeNull()
        ->and($agreement->status)->toBe('te_ondertekenen')
        ->and($agreement->mentor_approved_at)->not->toBeNull();
});

it('laat een mentor niet de stage van een andere mentor goedkeuren', function () {
    [, $stage] = mentorMetStagiair();

    $andereMentorUser = User::factory()->withRole('mentor')->create();
    Mentor::create(['user_id' => $andereMentorUser->id, 'company_id' => Company::factory()->create()->id]);

    Livewire::actingAs($andereMentorUser)->test(MentorDashboard::class)
        ->call('approve', $stage->id);
})->throws(ModelNotFoundException::class);
