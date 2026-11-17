<?php

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function roleAccessUserWithRole(string $roleName): User
{
    $role = Role::create(['name' => $roleName]);

    return User::factory()->create([
        'role_id' => $role->id,
        'status' => 1,
    ]);
}

test('eco auditor can view Laravel catalogs but cannot mutate them', function () {
    $this->actingAs(roleAccessUserWithRole('Ecoauditor'));

    $this->get(route('materials.index'))
        ->assertOk()
        ->assertDontSee('Editar')
        ->assertDontSee('Nuevo Material');
    $this->get(route('materials.create'))->assertForbidden();
    $this->post(route('materials.store'), [])->assertForbidden();
});

test('root sends guests to login and authenticated users to dashboard', function () {
    $this->get('/')->assertRedirect(route('login'));

    $this->actingAs(roleAccessUserWithRole('Ecoadmin'));

    $this->get('/')->assertRedirect(route('dashboard'));
});

test('eco admin can manage Laravel catalogs', function () {
    $this->actingAs(roleAccessUserWithRole('Ecoadmin'));

    $this->get(route('materials.create'))->assertOk();
});

test('eco operator cannot access Laravel Livewire and MVC pages', function () {
    $this->actingAs(roleAccessUserWithRole('Ecooperador'));

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('eco auditor cannot access the React API or receive a React token', function () {
    Sanctum::actingAs(roleAccessUserWithRole('Ecoauditor'));

    $this->getJson('/api/materials')->assertForbidden();
    $this->postJson('/api/collection-events', [])->assertForbidden();
    $this->postJson('/api/deliveries', [])->assertForbidden();
});

test('eco operator can read and write through React API', function () {
    Sanctum::actingAs(roleAccessUserWithRole('Ecooperador'));

    $this->getJson('/api/me')->assertOk();
    $this->postJson('/api/collection-events', [])->assertUnprocessable();
});

test('eco admin can write through the React API', function () {
    Sanctum::actingAs(roleAccessUserWithRole('Ecoadmin'));

    $this->postJson('/api/collection-events', [])->assertUnprocessable();
});

test('users with other roles cannot receive a React API token', function (string $roleName) {
    $user = roleAccessUserWithRole($roleName);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertForbidden();

    expect($user->tokens()->count())->toBe(0);
})->with(['Ecoauditor', 'Visitante']);
