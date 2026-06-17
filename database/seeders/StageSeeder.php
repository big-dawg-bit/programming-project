<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompetencyFramework;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Role;
use App\Models\Stage;
use App\Models\StageApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StageSeeder extends Seeder
{
    /**
     * Maakt eerst één basis-teststage aan voor de eerste student (altijd, ook tijdens
     * tests). Daarna — enkel buiten de testsuite — wordt rijke demo-data toegevoegd:
     * meerdere studenten met aanvragen in elke status, actieve stages, verspreide
     * weeklogs en evaluaties, zodat elk portaal "vol" oogt na `migrate:fresh --seed`.
     *
     * De testsuite blijft deterministisch: SeederTest verwacht exact één stage en
     * EvaluationFormTest gebruikt Evaluation::first(), dus de demo draait daar niet.
     *
     * Draai eerst de hoofd-seeder (php artisan db:seed) zodat de student,
     * mentor, docent en het framework bestaan.
     */
    public function run(): void
    {
        $student = Student::first();

        if (! $student) {
            $this->command?->warn('Geen student gevonden. Draai eerst: php artisan db:seed');

            return;
        }

        // --- Basis-teststage (altijd) ---
        $stage = Stage::firstOrCreate(
            ['student_id' => $student->id],
            [
                'company_id' => Company::first()?->id,
                'mentor_id' => Mentor::first()?->id,
                'docent_id' => Docent::first()?->id,
                'framework_id' => CompetencyFramework::first()?->id,
                'status' => 'active',
                'start_date' => now()->subWeeks(4)->toDateString(),
                'end_date' => now()->addWeeks(8)->toDateString(),
            ]
        );

        $this->command?->info("Teststage #{$stage->id} aangemaakt voor student #{$student->id}.");

        // Rijke demo-data niet tijdens tests (houdt SeederTest/EvaluationFormTest deterministisch).
        if (app()->runningUnitTests()) {
            return;
        }

        $this->seedDemodata($stage);
    }

    /** Vult de portalen met realistische data buiten de testsuite. */
    private function seedDemodata(Stage $linaStage): void
    {
        $companies = Company::all();
        $framework = CompetencyFramework::where('is_active', true)->first() ?? CompetencyFramework::first();
        $mentor = Mentor::first();
        $docent = Docent::first();
        $studentRoleId = Role::where('name', 'student')->value('id');

        // 1) Eerste student (Lina): goedgekeurde aanvraag + verspreide weeklogs + mid-term evaluatie.
        $lina = $linaStage->student;
        $aanvraagLina = $this->aanvraag($lina, $companies->first(), 'approved', 'Stagiair Software Development');
        $linaStage->update(['application_id' => $aanvraagLina->id]);
        $this->weeklogs($linaStage, 6);
        if ($framework) {
            $this->evaluatie($linaStage, $framework, 'mid-term');
        }

        // 2) Extra studenten met uiteenlopende situaties (vullen overzicht + reviewqueue).
        $scenarios = [
            ['naam' => 'Sam Peeters',  'email' => 'sam@ehb.be',   'nr' => 'EHB2025002', 'status' => 'submitted',         'titel' => 'Stagiair Webontwikkeling'],
            ['naam' => 'Yusuf Demir',  'email' => 'yusuf@ehb.be', 'nr' => 'EHB2025003', 'status' => 'changes_requested', 'titel' => 'Stagiair Data Engineering'],
            ['naam' => 'Marie Dubois', 'email' => 'marie@ehb.be', 'nr' => 'EHB2025004', 'status' => 'approved',          'titel' => 'Stagiair Cloud & DevOps'],
            ['naam' => 'Tom Claes',    'email' => 'tom@ehb.be',   'nr' => 'EHB2025005', 'status' => 'rejected',          'titel' => 'Stagiair Netwerkbeheer'],
        ];

        foreach ($scenarios as $i => $s) {
            $student = $this->student($s['naam'], $s['email'], $s['nr'], $studentRoleId);
            $company = $companies[($i + 1) % max($companies->count(), 1)];
            $aanvraag = $this->aanvraag($student, $company, $s['status'], $s['titel']);

            if ($s['status'] === 'rejected') {
                $aanvraag->reviews()->create([
                    'decision' => 'rejected',
                    'feedback' => 'Het voorgestelde bedrijf is niet erkend voor stages.',
                    'reviewed_at' => now()->subDays(5),
                ]);
            }

            if ($s['status'] === 'changes_requested') {
                $aanvraag->reviews()->create([
                    'decision' => 'changes_requested',
                    'feedback' => 'Gelieve de stageperiode en de mentorgegevens aan te vullen.',
                    'reviewed_at' => now()->subDays(2),
                ]);
            }

            // Goedgekeurd -> actieve stage met weeklogs + mid-term en final evaluatie.
            if ($s['status'] === 'approved') {
                $stage = $this->stageVoor($student, $company, $mentor, $docent, $framework, $aanvraag);
                $this->weeklogs($stage, 4);
                if ($framework) {
                    $this->evaluatie($stage, $framework, 'mid-term');
                    $this->evaluatie($stage, $framework, 'final');
                }
            }
        }

        $this->command?->info('Demo-data toegevoegd: meerdere studenten, stages, weeklogs en evaluaties.');
    }

    private function student(string $naam, string $email, string $nr, ?int $roleId): Student
    {
        [$first, $last] = array_pad(explode(' ', $naam, 2), 2, '');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $naam,
                'first_name' => $first,
                'last_name' => $last,
                'password' => Hash::make('password'),
                'role_id' => $roleId,
            ]
        );

        return Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'student_number' => $nr,
                'study_program' => 'Toegepaste Informatica',
                'academic_year' => '2025-2026',
            ]
        );
    }

    private function aanvraag(Student $student, ?Company $company, string $status, string $titel): StageApplication
    {
        return $student->applications()->create([
            'company_id' => $company?->id,
            'position_title' => $titel,
            'description' => 'Demo-aanvraag met status "'.$status.'".',
            'proposed_mentor_name' => 'Demo Mentor',
            'start_date' => now()->subWeeks(4)->toDateString(),
            'end_date' => now()->addWeeks(8)->toDateString(),
            'status' => $status,
            'submitted_at' => now()->subDays(10),
        ]);
    }

    private function stageVoor(Student $student, ?Company $company, ?Mentor $mentor, ?Docent $docent, ?CompetencyFramework $framework, StageApplication $aanvraag): Stage
    {
        return Stage::firstOrCreate(
            ['student_id' => $student->id],
            [
                'company_id' => $company?->id,
                'mentor_id' => $mentor?->id,
                'docent_id' => $docent?->id,
                'framework_id' => $framework?->id,
                'application_id' => $aanvraag->id,
                'status' => 'active',
                'start_date' => now()->subWeeks(4)->toDateString(),
                'end_date' => now()->addWeeks(8)->toDateString(),
            ]
        );
    }

    /** Maakt $aantal weeklogs aan, verspreid over de weken en met wisselende statussen. */
    private function weeklogs(Stage $stage, int $aantal): void
    {
        $statussen = ['approved', 'approved', 'submitted', 'draft'];

        for ($w = 1; $w <= $aantal; $w++) {
            $status = $statussen[($w - 1) % count($statussen)];

            $stage->weeklogs()->firstOrCreate(
                ['week_number' => $w],
                [
                    'period_start' => now()->subWeeks($aantal - $w + 1)->startOfWeek()->toDateString(),
                    'period_end' => now()->subWeeks($aantal - $w + 1)->endOfWeek()->toDateString(),
                    'content' => "Week {$w}: gewerkt aan demo-taken, overleg met mentor en uitwerking van features.",
                    'hours_worked' => 38,
                    'status' => $status,
                    'submitted_at' => $status === 'draft' ? null : now()->subWeeks($aantal - $w),
                ]
            );
        }
    }

    /** Maakt een ingediende evaluatie met scores en een gewogen eindcijfer (uit gewicht-snapshots). */
    private function evaluatie(Stage $stage, CompetencyFramework $framework, string $type): void
    {
        $competencies = $framework->competencies()->orderBy('sort_order')->get();

        if ($competencies->isEmpty()) {
            return;
        }

        $evaluation = $stage->evaluations()->create([
            'framework_id' => $framework->id,
            'type' => $type,
            'status' => 'submitted',
            'submitted_at' => now()->subDays($type === 'final' ? 1 : 20),
        ]);

        $totalWeight = 0;
        $weighted = 0;

        foreach ($competencies as $competency) {
            $score = $type === 'final' ? rand(70, 95) : rand(60, 85);

            $evaluation->scores()->create([
                'competency_id' => $competency->id,
                'weight_snapshot' => $competency->weight,
                'score' => $score,
            ]);

            $totalWeight += $competency->weight;
            $weighted += $score * $competency->weight;
        }

        $evaluation->update([
            'overall_score' => $totalWeight > 0 ? round($weighted / $totalWeight, 2) : 0,
        ]);
    }
}
