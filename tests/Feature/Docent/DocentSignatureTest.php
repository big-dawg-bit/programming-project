<?php

use App\Livewire\Docent\Dashboard as DocentDashboard;
use App\Models\Company;
use App\Models\Docent;
use App\Models\Stage;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

const GELDIGE_HANDTEKENING = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQAY3Y2wAAAAAElFTkSuQmCC';

/** Maakt een docent met een toegewezen stage waarvan de overeenkomst al bestaat (mentor gaf akkoord). */
function docentMetOvereenkomst(array $agreementAttrs = []): array
{
    $company = Company::factory()->create();

    $docentUser = User::factory()->withRole('docent')->create();
    $docent = Docent::create(['user_id' => $docentUser->id, 'department' => 'Toegepaste Informatica']);

    $studentUser = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $studentUser->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'approved',
    ]);
    $stage = Stage::create([
        'student_id' => $student->id,
        'docent_id' => $docent->id,
        'company_id' => $company->id,
        'application_id' => $application->id,
        'status' => 'active',
    ]);
    $agreement = StageAgreement::create(array_merge([
        'application_id' => $application->id,
        'status' => 'te_ondertekenen',
        'mentor_approved_at' => now(),
    ], $agreementAttrs));

    return [$docentUser, $stage, $agreement];
}

it('laat de docent tekenen; status blijft te_ondertekenen tot de student ook tekent', function () {
    [$docentUser, $stage, $agreement] = docentMetOvereenkomst();

    Livewire::actingAs($docentUser)->test(DocentDashboard::class)
        ->call('openSign', $stage->id)
        ->call('signDocent', GELDIGE_HANDTEKENING)
        ->assertHasNoErrors();

    $agreement->refresh();

    expect($agreement->docent_signature)->toBe(GELDIGE_HANDTEKENING)
        ->and($agreement->docent_signed_at)->not->toBeNull()
        ->and($agreement->status)->toBe('te_ondertekenen');
});

it('zet de overeenkomst op ingediend zodra student en docent beide getekend hebben', function () {
    [$docentUser, $stage, $agreement] = docentMetOvereenkomst([
        'student_signature' => GELDIGE_HANDTEKENING,
        'student_signed_at' => now(),
    ]);

    Livewire::actingAs($docentUser)->test(DocentDashboard::class)
        ->call('openSign', $stage->id)
        ->call('signDocent', GELDIGE_HANDTEKENING);

    expect($agreement->refresh()->status)->toBe('ingediend');
});

it('laat een docent niet de stage van een andere docent tekenen', function () {
    [, $stage] = docentMetOvereenkomst();

    $andereDocentUser = User::factory()->withRole('docent')->create();
    Docent::create(['user_id' => $andereDocentUser->id, 'department' => 'Elektronica']);

    Livewire::actingAs($andereDocentUser)->test(DocentDashboard::class)
        ->set('signingStageId', $stage->id)
        ->call('signDocent', GELDIGE_HANDTEKENING);
})->throws(ModelNotFoundException::class);
