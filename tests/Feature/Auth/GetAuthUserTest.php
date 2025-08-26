<?php

test('retrieve the logged in user', function () {
    $this->actAsUser();

    $response = $this->getJson('/api/user');
 
    $response->assertOk();
});

test('cannot retrieve user if not authenticated', function () {
    // do not login

    $response = $this->getJson('/api/user');

    $response->assertUnauthorized();
});
