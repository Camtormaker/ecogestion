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

    $this->get(route('users.index'))->assertForbidden();
    $this->get(route('users.create'))->assertForbidden();
    $this->post(route('users.store'))->assertForbidden();
    $this->get(route('users.edit', $target))->assertForbidden();
    $this->put(route('users.update', $target))->assertForbidden();
    $this->delete(route('users.destroy', $target))->assertForbidden();
})->with(['Operador', 'Auditor']);

test('administrators can access user administration', function (string $roleName) {
    $this->actingAs(userWithRole($roleName));

    $this->get(route('users.index'))->assertOk();
})->with(['Admin', 'Administrador']);

test('operators do not see the administration link in the navigation', function () {
    $this->actingAs(userWithRole('Operador'));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(route('users.index'));
});
