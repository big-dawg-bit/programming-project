<?php

use App\Models\User;

// deze gaat gaan controleren of de loginpagina nog overeenkomt met het EhB-ontwerp ofni
it('toont de gestylede EhB-loginpagina', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Welkom terug')
        ->assertSee('EhB-mailadres')
        ->assertSee('Inloggen')
        ->assertSee('Wachtwoord vergeten?');
});

// deze gaat controleren of de pagina "Wachtwoord vergeten" correct wordt weergegeven ofni
it('toont de wachtwoord-vergeten pagina', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('Wachtwoord vergeten')
        ->assertSee('Stuur reset-link')
        ->assertSee('Terug naar inloggen');
});

// gaat controleren of gebruikers nog degelijk kunnen inloggen
// nadat de styling van de authenticatiepagina's werd aangepastt
it('breekt de login-functionaliteit niet', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect();

    $this->assertAuthenticated();
});
