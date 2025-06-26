<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the last achievement from dashboard API', function () {
    $user = \App\Models\User::where('email', 'admin@example.com');
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/api/dashboard');

    $response->assertOk()->assertJsonStructure(['lastAchievement']);
});
