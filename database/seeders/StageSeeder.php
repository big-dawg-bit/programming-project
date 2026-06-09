<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompetencyFramework;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Stage;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Maakt één teststage aan voor de eerste student in de database,
     * zodat we weeklogs en een eindrapport kunnen testen.
     *
     * Draai eerst de hoofd-seeder (php artisan db:seed) zodat de student,
     * mentor, docent en het framework bestaan.
     */
    public function run(): void
    {
        $student = Student::first();

        if (! $student) {
            $this->command->warn('Geen student gevonden. Draai eerst: php artisan db:seed');

            return;
        }

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

        $this->command->info("Teststage #{$stage->id} aangemaakt voor student #{$student->id}.");
    }
}
