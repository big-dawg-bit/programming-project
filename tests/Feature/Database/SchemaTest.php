<?php

use Illuminate\Support\Facades\Schema;

/*
 | Test of de migraties de verwachte structuur opleveren.
 | Heeft geen data nodig: RefreshDatabase draait alle migraties vooraf.
 | Plaats in: tests/Feature/Database/SchemaTest.php
 */

it('heeft alle kerntabellen', function () {
    $tables = [
        'users', 'roles', 'students', 'docenten', 'mentors', 'companies',
        'competency_frameworks', 'competencies', 'files',
        'stage_applications', 'application_reviews', 'stage_agreements',
        'stages', 'weeklogs', 'weeklog_comments', 'final_reports',
        'evaluations', 'evaluation_scores', 'notifications', 'audit_logs',
    ];

    foreach ($tables as $table) {
        expect(Schema::hasTable($table))->toBeTrue("Tabel '{$table}' ontbreekt");
    }
});

it('bewaart een weight_snapshot op evaluation_scores', function () {
    // Kern van het configureerbare evaluatie-ontwerp: het gewicht wordt
    // op het moment van scoren vastgelegd, los van de actuele competentie.
    expect(Schema::hasColumns('evaluation_scores', [
        'evaluation_id', 'competency_id', 'weight_snapshot', 'score',
    ]))->toBeTrue();
});

it('versioneert competency frameworks', function () {
    expect(Schema::hasColumns('competency_frameworks', [
        'name', 'version', 'is_active',
    ]))->toBeTrue();
});

it('koppelt een competentie aan een framework met een gewicht', function () {
    expect(Schema::hasColumns('competencies', [
        'framework_id', 'title', 'weight', 'sort_order',
    ]))->toBeTrue();
});
