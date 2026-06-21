<?php

use App\Livewire\Mentor\DocumentList as MentorDocs;
use App\Livewire\Student\DocumentList as StudentDocs;
use App\Models\Company;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Stage;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

/** Bouwt een volledig dossier: student + goedgekeurde aanvraag + overeenkomst + stage (mentor/docent) + logboek. */
function studentMetDossier(): array
{
    $studentUser = User::factory()->withRole('student')->create(['name' => 'Lina Test']);
    $student = Student::factory()->create(['user_id' => $studentUser->id]);
    $company = Company::factory()->create(['name' => 'Acme NV']);

    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'approved',
        'position_title' => 'Stagiair Software',
    ]);

    $mentorUser = User::factory()->withRole('mentor')->create();
    $mentor = Mentor::create(['user_id' => $mentorUser->id, 'company_id' => $company->id]);
    $docent = Docent::create(['user_id' => User::factory()->withRole('docent')->create()->id]);

    $stage = Stage::create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'application_id' => $application->id,
        'mentor_id' => $mentor->id,
        'docent_id' => $docent->id,
        'status' => 'active',
        'start_date' => now()->subWeek(),
        'end_date' => now()->addWeeks(8),
    ]);

    StageAgreement::create([
        'application_id' => $application->id,
        'status' => 'te_ondertekenen',
    ]);

    $stage->weeklogs()->create([
        'week_number' => 1,
        'content' => 'Eerste week gewerkt aan de stage-app.',
        'status' => 'ingediend',
        'submitted_at' => now(),
    ]);

    return [$studentUser, $mentorUser, $stage];
}

it('toont de student zijn aanvraag, overeenkomst en logboeken leesbaar in Documenten', function () {
    [$studentUser] = studentMetDossier();

    $component = Livewire::actingAs($studentUser)->test(StudentDocs::class);

    // Stageaanvraag (standaardtab) toont de echte aanvraaginhoud.
    $component->assertSee('Stagiair Software');

    // Overeenkomst is leesbaar als formeel document ín Documenten.
    $component->set('tab', 'Stageovereenkomst')->assertSee('Artikel 1 — Partijen');

    // Logboeken zijn leesbaar ín Documenten.
    $component->set('tab', 'Logboeken')->assertSee('Logboek week 1');
});

it('laat de mentor de documenten per stagiair raadplegen', function () {
    [, $mentorUser, $stage] = studentMetDossier();

    $component = Livewire::actingAs($mentorUser)->test(MentorDocs::class);

    // De lijst toont de stagiair.
    $component->assertSee('Lina Test');

    // Klikken opent de leesbare documenten van die student.
    $component->call('openStudent', $stage->id)
        ->assertSee('Stagiair Software')
        ->assertSee('Artikel 1 — Partijen');
});

it('laat een mentor geen documenten van een vreemde student zien', function () {
    [, $mentorUser] = studentMetDossier();
    // Tweede, niet-begeleide dossier.
    [, , $vreemdeStage] = studentMetDossier();

    Livewire::actingAs($mentorUser)->test(MentorDocs::class)
        ->call('openStudent', $vreemdeStage->id)
        ->assertDontSee('Artikel 1 — Partijen');
});
