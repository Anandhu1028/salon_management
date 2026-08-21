<?php

use App\Models\Role;
use App\Models\User;

it('redirects unauthenticated user to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('allows authenticated user to view dashboard', function () {
    $role = Role::firstOrCreate(['name' => 'administrator']);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});
