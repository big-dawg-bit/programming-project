<?php

use App\Models\CompetencyFramework;
use App\Models\Role;
use App\Models\Stage;
use App\Models\Student;
use Database\Seeders\StageSeeder;

/*
 | Test dat de seeders draaien en consistente data opleveren.
 | RefreshDatabase geeft een lege, gemigreerde DB; wij seeden expliciet.
 | Plaats in: tests/Feature/Database/SeederTest.php
 */

it('seedt de vijf rollen', function () {
    $this->seed();

    expect(Role::pluck('name')->sort()->values()->all())
        ->toBe(['admin', 'docent', 'mentor', 'stagecommissie', 'student']);
});

it('seedt een student met subtype', function () {
    $this->seed();

    $student = Student::first();
    expect($student)->not->toBeNull()
        ->and($student->user->email)->toBe('student@ehb.be');
});

it('seedt competenties waarvan de gewichten optellen tot 100', function () {
    // De kern-businessregel van het configureerbare evaluatiemodel.
    $this->seed();

    $framework = CompetencyFramework::first();
    expect((int) $framework->competencies()->sum('weight'))->toBe(100);
});

it('maakt een teststage aan via de StageSeeder', function () {
    $this->seed();            // hoofd-seeder eerst
    $this->seed(StageSeeder::class);

    expect(Stage::count())->toBe(1);
    expect(Stage::first()->student_id)->toBe(Student::first()->id);
});
