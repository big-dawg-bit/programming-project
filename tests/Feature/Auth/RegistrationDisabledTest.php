<?php

use Illuminate\Support\Facades\Route;

// Onze applicatie ondersteunt GEEN zelfregistratie. Studenten, docenten,
// administratie en mentoren krijgen hun account op een andere manier.
// Deze tests bewaken dat registratie volledig ontoegankelijk blijft.

it('maakt de registratiepagina niet bereikbaar (GET /register -> 404)', function () {
    $this->get('/register')->assertNotFound();
});

it('weigert een registratiepoging (POST /register -> 404)', function () {
    $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    // Er mag absoluut geen account zijn aangemaakt of ingelogd.
    $this->assertGuest();
});

it('registreert geen benoemde register-routes', function () {
    expect(Route::has('register'))->toBeFalse();
    expect(Route::has('register.store'))->toBeFalse();
});
