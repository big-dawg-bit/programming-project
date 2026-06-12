<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\StageApplication;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/** @extends Factory<StageApplication> */
class StageApplicationFactory extends Factory
{
    use HasFactory;

    protected $model = StageApplication::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'company_id' => Company::factory(),
            'position_title' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-20',
            'status' => 'submitted',
        ];
    }
}
