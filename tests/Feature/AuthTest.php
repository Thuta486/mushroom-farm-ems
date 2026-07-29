<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('employees.index'))->assertRedirect(route('login'));
});

test('users can log in and reach the dashboard', function () {
    $user = User::factory()->create([
        'email' => 'manager@test.com',
        'password' => bcrypt('password'),
    ]);

    $this->post(route('login'), [
        'email' => 'manager@test.com',
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('users can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
