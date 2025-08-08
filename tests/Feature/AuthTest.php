<?php

test('retrieve the logged in user', function () {
    $this->actAsUser();
 
    $response = $this->getJson('/api/user');
 
    $response->assertOk();
});
