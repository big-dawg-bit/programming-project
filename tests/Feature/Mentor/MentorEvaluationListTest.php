<?php

use App\Livewire\Mentor\EvaluationList;
use App\Models\Company;
use App\Models\Mentor;
use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

it('toont alle stagiairs van de mentor in de evaluatielijst, niet enkel de eerste', function () {
    $company = Company::factory()->create();
    $mentorUser = User::factory()->withRole('mentor')->create();
    $mentor = Mentor::create(['user_id' => $mentorUser->id, 'company_id' => $company->id]);

    // Twee verschillende studenten, beide aan dezelfde mentor toegewezen.
    foreach (['Eerste Student', 'Tweede Student'] as $naam) {
        $studentUser = User::factory()->withRole('student')->create(['name' => $naam]);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);
        Stage::create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'mentor_id' => $mentor->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addWeeks(8),
        ]);
    }

    // Beide stagiairs moeten zichtbaar zijn (vroeger enkel de eerste).
    Livewire::actingAs($mentorUser)->test(EvaluationList::class)
        ->assertSee('Eerste Student')
        ->assertSee('Tweede Student');
});
