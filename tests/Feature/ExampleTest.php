<?php

test('redirects home to dashboard', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect('/dashboard');
});
