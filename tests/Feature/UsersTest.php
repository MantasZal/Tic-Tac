<?php

test('home page is accessible', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
