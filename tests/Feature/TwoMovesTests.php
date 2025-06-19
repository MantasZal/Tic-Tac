<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('example', function () {
    $user = \App\Models\User::where('email', 'admin@example.com');
    $user = User::factory()->create();

    $response->assertStatus(200);
});
