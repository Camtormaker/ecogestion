<?php

use App\Models\Role;
use App\Models\User;

function userWithRole(string $roleName): User
{
    $role = Role::create(['name' => $roleName]);

    return User::factory()->create(['role_id' => $role->id]);
}

test('operators and auditors cannot access user administration', function (string $roleName) {
    $user = userWithRole($roleName);
    $target = User::factory()->create(['role_id' => $user->role_id]);

    $this->actingAs($user);

    $response = $this->get(route('users.index'));

    if ($user->isOperator()) {
        $response->assertRedirect(route('login'));
        $this->assertGuest();

        return;
    }

    $response->assertForbidden();
    $this->get(route('users.create'))->assertForbidden();
    $this->post(route('users.store'))->assertForbidden();
    $this->get(route('users.edit', $target))->assertForbidden();
    $this->put(route('users.update', $target))->assertForbidden();
    $this->delete(route('users.destroy', $target))->assertForbidden();
})->with(['Operador', 'Auditor']);

test('eco admins can access user administration', function (string $roleName) {
    $this->actingAs(userWithRole($roleName));

    $this->get(route('users.index'))->assertOk();
})->with(['Ecoadmin', 'Admin', 'Administrador']);

test('auditors can access the dashboard without the administration link', function () {
    $this->actingAs(userWithRole('Ecoauditor'));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(route('users.index'));
});

test('operators cannot access the Laravel dashboard', function () {
    $this->actingAs(userWithRole('Ecooperador'));

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});
