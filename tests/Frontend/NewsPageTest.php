<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    //dd($response->getVary());

    $response->assertStatus(200);
});
