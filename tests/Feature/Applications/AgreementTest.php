<?php

use App\Livewire\Applications\AgreementReview;
use App\Livewire\Student\AgreementUpload;
use App\Models\Company;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Stage;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/** Maakt een student met een goedgekeurde aanvraag (waar een overeenkomst bij hoort). */
function studentMetGoedgekeurdeAanvraag(): array
{
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $company = Company::factory()->create();
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'approved',
    ]);

    // Goedgekeurde aanvraag => stage mét toegewezen mentor + docent, zodat de
    // overeenkomst ondertekend mag worden (tekenen kan pas na die koppeling).
    $mentor = Mentor::create([
        'user_id' => User::factory()->withRole('mentor')->create()->id,
        'company_id' => $company->id,
    ]);
    $docent = Docent::create(['user_id' => User::factory()->withRole('docent')->create()->id]);

    Stage::create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'application_id' => $application->id,
        'mentor_id' => $mentor->id,
        'docent_id' => $docent->id,
        'status' => 'active',
        'start_date' => now(),
        'end_date' => now()->addWeeks(8),
    ]);

    return [$user, $application];
}

it('laat een student de getekende overeenkomst opladen', function () {
    Storage::fake('local');
    [$user, $application] = studentMetGoedgekeurdeAanvraag();

    Livewire::actingAs($user)->test(AgreementUpload::class)
        ->set('upload', UploadedFile::fake()->create('overeenkomst.pdf', 200, 'application/pdf'))
        ->set('insuranceConfirmed', true)
        ->call('save')
        ->assertHasNoErrors();

    $agreement = StageAgreement::where('application_id', $application->id)->first();

    expect($agreement)->not->toBeNull()
        ->and($agreement->status)->toBe('ingediend')
        ->and((bool) $agreement->insurance_confirmed)->toBeTrue();
});

it('laat een student de overeenkomst ondertekenen op de trackpad', function () {
    [$user, $application] = studentMetGoedgekeurdeAanvraag();

    $handtekening = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQAY3Y2wAAAAAElFTkSuQmCC';

    Livewire::actingAs($user)->test(AgreementUpload::class)
        ->call('signStudent', $handtekening)
        ->assertHasNoErrors();

    $agreement = StageAgreement::where('application_id', $application->id)->first();

    // Alleen de student tekende: nog niet 'ingediend' (de docent moet ook tekenen).
    expect($agreement)->not->toBeNull()
        ->and($agreement->student_signature)->toBe($handtekening)
        ->and($agreement->student_signed_at)->not->toBeNull()
        ->and($agreement->status)->toBe('te_ondertekenen');
});

it('blokkeert ondertekenen zolang er geen mentor en docent is toegewezen', function () {
    // Goedgekeurde aanvraag met een stage zónder mentor/docent.
    $user = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $company = Company::factory()->create();
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'approved',
    ]);
    Stage::create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'application_id' => $application->id,
        'mentor_id' => null,
        'docent_id' => null,
        'status' => 'active',
        'start_date' => now(),
        'end_date' => now()->addWeeks(8),
    ]);

    $handtekening = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQAY3Y2wAAAAAElFTkSuQmCC';

    Livewire::actingAs($user)->test(AgreementUpload::class)
        ->call('signStudent', $handtekening);

    // Geen handtekening opgeslagen zolang mentor/docent ontbreken.
    expect(StageAgreement::where('application_id', $application->id)
        ->whereNotNull('student_signature')->exists())->toBeFalse();
});

it('laat een student niet opnieuw tekenen nadat hij al getekend heeft', function () {
    [$user, $application] = studentMetGoedgekeurdeAanvraag();

    $eerste = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQAY3Y2wAAAAAElFTkSuQmCC';
    $tweede = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $component = Livewire::actingAs($user)->test(AgreementUpload::class);
    $component->call('signStudent', $eerste);
    $component->call('signStudent', $tweede);

    // De eerste handtekening blijft staan; de tweede wordt genegeerd.
    expect(StageAgreement::where('application_id', $application->id)->first()->student_signature)
        ->toBe($eerste);
});

it('weigert een handtekening die geen geldige PNG-dataURL is', function () {
    [$user, $application] = studentMetGoedgekeurdeAanvraag();

    Livewire::actingAs($user)->test(AgreementUpload::class)
        ->call('signStudent', 'gewoon-wat-tekst')
        ->assertHasErrors('signature');

    expect(StageAgreement::where('application_id', $application->id)->exists())->toBeFalse();
});

it('weigert opladen zonder bevestigde verzekering', function () {
    Storage::fake('local');
    [$user] = studentMetGoedgekeurdeAanvraag();

    Livewire::actingAs($user)->test(AgreementUpload::class)
        ->set('upload', UploadedFile::fake()->create('overeenkomst.pdf', 200, 'application/pdf'))
        ->set('insuranceConfirmed', false)
        ->call('save')
        ->assertHasErrors(['insuranceConfirmed']);
});

it('laat de stagecommissie een overeenkomst bevestigen', function () {
    [, $application] = studentMetGoedgekeurdeAanvraag();

    $agreement = StageAgreement::create([
        'application_id' => $application->id,
        'status' => 'ingediend',
        'insurance_confirmed' => true,
        'uploaded_at' => now(),
    ]);

    $commissie = User::factory()->withRole('stagecommissie')->create();

    Livewire::actingAs($commissie)->test(AgreementReview::class)
        ->call('confirm', $agreement->id);

    expect($agreement->fresh()->status)->toBe('bevestigd');
});

it('blokkeert een niet-commissielid op het overeenkomsten-overzicht', function () {
    [$studentUser] = studentMetGoedgekeurdeAanvraag();

    $this->actingAs($studentUser)->get('/overeenkomsten')->assertForbidden();
});
