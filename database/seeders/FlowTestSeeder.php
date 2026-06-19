<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FlowTestSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('name', 'student')->first();

        $user = User::firstOrCreate(
            ['email' => 'flow@ehb.be'],
            [
                'name' => 'Flow Teststudent',
                'first_name' => 'Flow',
                'last_name' => 'Teststudent',
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
            ]
        );

        Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'student_number' => 'EHB2025777',
                'study_program' => 'Toegepaste Informatica',
                'academic_year' => '2025-2026',
            ]
        );
    }
}
