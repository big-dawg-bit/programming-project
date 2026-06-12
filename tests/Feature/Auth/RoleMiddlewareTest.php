<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:admin'])
        ->get('/_test/admin-only', fn () => 'ok');
});

it('blokkeert een verkeerde rol met 403', function () {
    $student = User::factory()->withRole('student')->create();

    $this->actingAs($student)->get('/_test/admin-only')->assertForbidden();
});

it('laat de juiste rol door', function () {
    $admin = User::factory()->withRole('admin')->create();

    $this->actingAs($admin)->get('/_test/admin-only')->assertOk()->assertSee('ok');
});

it('stuurt gasten naar de login', function () {
    $this->get('/_test/admin-only')->assertRedirect('/login');
});
