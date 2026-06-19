<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Competency;
use App\Models\CompetencyFramework;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Roles (supertype/subtype design) ---
        $roles = [];
        foreach (['student', 'stagecommissie', 'docent', 'mentor', 'admin', 'bedrijf'] as $name) {
            $roles[$name] = Role::create(['name' => $name]);
        }

        // --- Companies ---
        $easi = Company::create([
            'name' => 'Easi',
            'contact_email' => 'contact@easi.net',
            'vat_number' => 'BE0123456789',
        ]);
        Company::create(['name' => 'Odoo', 'contact_email' => 'contact@odoo.com']);
        Company::create(['name' => 'Ingram Micro', 'contact_email' => 'contact@ingrammicro.be']);

        // --- Student user + subtype ---
        $studentUser = User::create([
            'name' => 'Lina Janssens',
            'first_name' => 'Lina',
            'last_name' => 'Janssens',
            'email' => 'student@ehb.be',
            'password' => Hash::make('password'),
            'role_id' => $roles['student']->id,
        ]);
        Student::create([
            'user_id' => $studentUser->id,
            'student_number' => 'EHB2025001',
            'study_program' => 'Toegepaste Informatica',
            'academic_year' => '2025-2026',
        ]);

        // --- Docent user + subtype ---
        $docentUser = User::create([
            'name' => 'Docent EhB',
            'first_name' => 'Docent',
            'last_name' => 'EhB',
            'email' => 'docent@ehb.be',
            'password' => Hash::make('password'),
            'role_id' => $roles['docent']->id,
        ]);
        Docent::create([
            'user_id' => $docentUser->id,
            'department' => 'Toegepaste Informatica',
        ]);

        // --- Mentor user + subtype ---
        $mentorUser = User::create([
            'name' => 'Margaux Schodts',
            'first_name' => 'Margaux',
            'last_name' => 'Schodts',
            'email' => 'mentor@easi.net',
            'password' => Hash::make('password'),
            'role_id' => $roles['mentor']->id,
        ]);
        Mentor::create([
            'user_id' => $mentorUser->id,
            'company_id' => $easi->id,
            'job_title' => 'Lead Developer',
            'phone' => '+32 470 00 00 00',
        ]);

        // --- Extra mentor (bedrijf) — handig om de mentor-/ondertekenflow te testen ---
        $mentorBedrijfUser = User::create([
            'name' => 'Mentor Bedrijf',
            'first_name' => 'Mentor',
            'last_name' => 'Bedrijf',
            'email' => 'mentor-bedrijf@bedrijf.be',
            'password' => Hash::make('password'),
            'role_id' => $roles['mentor']->id,
        ]);
        Mentor::create([
            'user_id' => $mentorBedrijfUser->id,
            'company_id' => $easi->id,
            'job_title' => 'Stagementor',
            'phone' => '+32 470 11 11 11',
        ]);

        // --- Stagecommissie user ---
        $commissieUser = User::create([
            'name' => 'Stagecommissie',
            'first_name' => 'Stage',
            'last_name' => 'Commissie',
            'email' => 'commissie@ehb.be',
            'password' => Hash::make('password'),
            'role_id' => $roles['stagecommissie']->id,
        ]);

        // --- Admin user ---
        $adminUser = User::create([
            'name' => 'Admin EhB',
            'first_name' => 'Admin',
            'last_name' => 'EhB',
            'email' => 'admin@ehb.be',
            'password' => Hash::make('password'),
            'role_id' => $roles['admin']->id,
        ]);

        // --- Bedrijf user (gekoppeld aan Easi) ---
        $bedrijfUser = User::create([
            'name' => 'Easi (bedrijf)',
            'first_name' => 'Easi',
            'last_name' => 'Bedrijf',
            'email' => 'bedrijf@easi.net',
            'password' => Hash::make('password'),
            'role_id' => $roles['bedrijf']->id,
        ]);
        $easi->update(['user_id' => $bedrijfUser->id]);

        // --- Configurable competency framework (versioned) ---
        $framework = CompetencyFramework::create([
            'name' => 'TI Stage Framework',
            'study_program' => 'Toegepaste Informatica',
            'version' => 1,
            'is_active' => true,
            'created_by' => $commissieUser->id,
        ]);

        // D1-D11 rubriek -- gewichten samen 100, volledig bewerkbaar at runtime
        $competencies = [
            ['code' => 'D1',  'title' => 'De lerende professional beheerst het volledige project- of operationeel planningsproces', 'weight' => 10],
            ['code' => 'D2',  'title' => 'De lerende professional ontwerpt IT-oplossingen volgens de industriestandaarden', 'weight' => 10],
            ['code' => 'D3',  'title' => 'De lerende professional implementeert digitale producten in een professionele omgeving', 'weight' => 10],
            ['code' => 'D4',  'title' => 'De lerende professional integreert technologie en infrastructuur binnen een professionele omgeving', 'weight' => 10],
            ['code' => 'D5',  'title' => 'De lerende professional hanteert een onderzoekende houding om tot innovatieve oplossingen te komen', 'weight' => 9],
            ['code' => 'D6',  'title' => 'De lerende professional communiceert helder en transparant in een professionele omgeving en/of in teamverband', 'weight' => 9],
            ['code' => 'D7',  'title' => 'De lerende professional denkt kritisch na om problemen efficiënt en effectief op te lossen', 'weight' => 9],
            ['code' => 'D8',  'title' => 'De lerende professional ziet persoonlijke ontwikkeling als de basis voor professionele groei', 'weight' => 9],
            ['code' => 'D9',  'title' => 'De lerende professional ontwikkelt een professionele attitude en handelt kwaliteitsvol', 'weight' => 8],
            ['code' => 'D10', 'title' => 'De lerende professional demonstreert ondernemend handelen in functie van waardecreatie', 'weight' => 8],
            ['code' => 'D11', 'title' => 'De lerende professional handelt ethisch en deontologisch', 'weight' => 8],
        ];
        foreach ($competencies as $i => $c) {
            Competency::create([
                'framework_id' => $framework->id,
                'code' => $c['code'],
                'title' => $c['title'],
                'weight' => $c['weight'],
                'sort_order' => $i + 1,
            ]);
        }

        // --- Teststage klaar voor evaluatie (feature/evaluations) ---
        // Koppelt een stage aan het framework hierboven, zodat er na het
        // seeden meteen een stage bestaat die geëvalueerd kan worden.
        $this->call(StageSeeder::class);
    }
}
