<?php

use App\Models\User;
use App\Models\Role;

beforeEach(fn () => $this->seed());

function makeUser(string $roleName): User {
    return User::factory()->create([
        'role_id' => Role::where('name', $roleName)->value('id'),
    ]);
}

it('laat een admin de gebruikerslijst openen', function () {
    $this->actingAs(makeUser('admin'))->get('/admin/users')->assertOk();
});

it('blokkeert een niet-admin met 403', function () {
    $this->actingAs(makeUser('student'))->get('/admin/users')->assertForbidden();
});
