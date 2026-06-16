<?php

test('la pagina principal carga correctamente', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
