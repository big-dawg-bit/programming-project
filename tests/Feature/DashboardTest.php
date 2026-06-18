<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users without a role see the default dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('students are redirected from the dashboard to their portal', function () {
    $user = User::factory()->withRole('student')->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('student.dashboard'));
});

test('the stagecommissie is redirected from the dashboard to the review queue', function () {
    $user = User::factory()->withRole('stagecommissie')->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('applications.review'));
});

test('docenten are redirected from the dashboard to their portal', function () {
    $user = User::factory()->withRole('docent')->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('docent.dashboard'));
});

test('mentoren are redirected from the dashboard to their portal', function () {
    $user = User::factory()->withRole('mentor')->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('mentor.dashboard'));
});

test('admins are redirected from the dashboard to the admin dashboard', function () {
    $user = User::factory()->withRole('admin')->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
});

test('the docent dashboard renders for a docent', function () {
    $user = User::factory()->withRole('docent')->create();

    $this->actingAs($user)
        ->get(route('docent.dashboard'))
        ->assertOk()
        ->assertSee('Mijn stages');
});

test('the mentor dashboard renders for a mentor', function () {
    $user = User::factory()->withRole('mentor')->create();

    $this->actingAs($user)
        ->get(route('mentor.dashboard'))
        ->assertOk()
        ->assertSee('Mijn stagiairs');
});

test('a student cannot reach the docent dashboard', function () {
    $user = User::factory()->withRole('student')->create();

    $this->actingAs($user)
        ->get(route('docent.dashboard'))
        ->assertForbidden();
});

test('a mentor cannot reach the docent dashboard', function () {
    $user = User::factory()->withRole('mentor')->create();

    $this->actingAs($user)
        ->get(route('docent.dashboard'))
        ->assertForbidden();
});
