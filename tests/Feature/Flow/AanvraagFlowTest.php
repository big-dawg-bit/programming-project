<?php

use App\Livewire\Applications\ReviewQueue;
use App\Livewire\Bedrijf\Aanvragen;
use App\Livewire\Bedrijf\Overeenkomsten;
use App\Models\Company;
use App\Models\CompetencyFramework;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

function maakAanvraag(string $companyStatus = 'pending'): array
{
    $bedrijfUser = User::factory()->withRole('bedrijf')->create();
    $company = Company::factory()->create(['user_id' => $bedrijfUser->id]);
    CompetencyFramework::firstOrCreate(['name' => 'Std'], ['is_active' => true, 'version' => 1]);

    $studentUser = User::factory()->withRole('student')->create();
    $student = Student::factory()->create(['user_id' => $studentUser->id]);
    $application = StageApplication::factory()->create([
        'student_id' => $student->id,
        'company_id' => $company->id,
        'status' => 'submitted',
        'company_status' => $companyStatus,
    ]);

    return [$bedrijfUser, $application];
}

it('verbergt de aanvraag voor de commissie tot het bedrijf accepteert', function () {
    [$bedrijfUser, $application] = maakAanvraag('pending');
    $commissie = User::where('email', 'commissie@ehb.be')->first();
    $naam = $application->student->user->name;

    // commissie ziet ze nog niet
    Livewire::actingAs($commissie)->test(ReviewQueue::class)->assertDontSee($naam);

    // bedrijf accepteert
    Livewire::actingAs($bedrijfUser)->test(Aanvragen::class)->call('accept', $application->id);
    expect($application->refresh()->company_status)->toBe('accepted');

    // nu ziet de commissie ze wel
    Livewire::actingAs($commissie)->test(ReviewQueue::class)->assertSee($naam);
});

it('maakt stage + framework + overeenkomst bij goedkeuring', function () {
    [, $application] = maakAanvraag('accepted');
    $commissie = User::where('email', 'commissie@ehb.be')->first();

    Livewire::actingAs($commissie)->test(ReviewQueue::class)->call('approve', $application->id);

    $application->refresh()->load('stage', 'agreement');
    expect($application->status)->toBe('approved')
        ->and($application->stage->framework_id)->not->toBeNull()
        ->and($application->agreement->status)->toBe('te_ondertekenen');
});

it('zet de overeenkomst op ingediend zodra student en bedrijf tekenen', function () {
    [$bedrijfUser, $application] = maakAanvraag('accepted');
    $commissie = User::where('email', 'commissie@ehb.be')->first();
    Livewire::actingAs($commissie)->test(ReviewQueue::class)->call('approve', $application->id);

    // Admin koppelt een docent + bedrijf koppelt een mentor: vereist vóór tekenen.
    $mentor = Mentor::create([
        'user_id' => User::factory()->withRole('mentor')->create()->id,
        'company_id' => $application->company_id,
    ]);
    $docent = Docent::create(['user_id' => User::factory()->withRole('docent')->create()->id]);
    $application->fresh()->stage->update(['mentor_id' => $mentor->id, 'docent_id' => $docent->id]);

    $handtekening = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    $agreement = $application->fresh()->agreement;

    // student tekent → nog niet ingediend
    $agreement->update(['student_signature' => $handtekening, 'student_signed_at' => now()]);
    $agreement->syncSignatureStatus();
    expect($agreement->refresh()->status)->toBe('te_ondertekenen');

    // bedrijf tekent via Overeenkomsten → ingediend
    Livewire::actingAs($bedrijfUser)->test(Overeenkomsten::class)
        ->call('openSign', $agreement->id)
        ->call('signCompany', $handtekening);

    expect($agreement->refresh()->status)->toBe('ingediend')
        ->and($agreement->company_signature)->not->toBeNull();
});
