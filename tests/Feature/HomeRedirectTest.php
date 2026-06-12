<?php

// Bezoekers mogen de standaard Laravel-welkomstpagina niet meer zien.
// Wie naar "/" gaat, wordt automatisch naar de loginpagina gestuurd.

it('stuurt de startpagina door naar de loginpagina', function () {
    $this->get('/')->assertRedirect('/login');
});
