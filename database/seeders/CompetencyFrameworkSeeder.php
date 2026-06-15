<?php

namespace Database\Seeders;

use App\Models\Competency;
use App\Models\CompetencyFramework;
use Illuminate\Database\Seeder;

class CompetencyFrameworkSeeder extends Seeder
{
    public function run(): void
    {
        $framework = CompetencyFramework::create([
            'name' => 'Stage-evaluatie Toegepaste Informatica',
            'study_program' => 'Toegepaste Informatica',
            'version' => 1,
            'is_active' => true,
        ]);

        $competencies = [
            ['code' => 'TC',  'title' => 'Technische competentie',      'weight' => 30],
            ['code' => 'COM', 'title' => 'Communicatie',                'weight' => 20],
            ['code' => 'SAM', 'title' => 'Samenwerken',                 'weight' => 20],
            ['code' => 'ZEL', 'title' => 'Zelfstandigheid',             'weight' => 15],
            ['code' => 'ATT', 'title' => 'Professionele attitude',      'weight' => 15],
        ];

        foreach ($competencies as $index => $data) {
            Competency::create([
                'framework_id' => $framework->id,
                'code' => $data['code'],
                'title' => $data['title'],
                'weight' => $data['weight'],
                'sort_order' => $index + 1,
            ]);
        }
    }
}
