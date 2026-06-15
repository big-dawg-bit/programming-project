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

test('admins are redirected from the dashboard to user management', function () {
    $user = User::factory()->withRole('admin')->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('admin.users'));
});
