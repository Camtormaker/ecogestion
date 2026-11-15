<?php

use App\Models\CollectionPoint;
use App\Models\Material;
use App\Models\RecyclingCenter;
use App\Models\Role;
use App\Models\User;

dataset('module one catalogs', [
    'materials' => ['materials', Material::class, ['name' => 'Material audit', 'category' => 'papel', 'unit' => 'kg', 'status' => 1]],
    'points' => ['points', CollectionPoint::class, ['name' => 'Point audit', 'type' => 'comunidad', 'address' => 'Zona 1', 'status' => 1]],
    'centers' => ['centers', RecyclingCenter::class, ['name' => 'Center audit', 'address' => 'Zona 2', 'email' => 'audit@example.com', 'status' => 1]],
]);

test('module one administrator can create read search update deactivate and delete an unused catalog record', function ($resource, $model, $data) {
    $role = Role::create(['name' => 'Administrador']);
    $this->actingAs(User::factory()->create(['role_id' => $role->id]));
    $this->get(route("{$resource}.create"))->assertOk();
    $this->post(route("{$resource}.store"), $data)->assertRedirect(route("{$resource}.index"))->assertSessionHasNoErrors();
    $record = $model::where('name', $data['name'])->firstOrFail();
    $this->get(route("{$resource}.index"))->assertOk()->assertSee($data['name']);
    $this->get(route("{$resource}.index", ['search' => $data['name']]))->assertOk()->assertSee($data['name']);
    $this->get(route("{$resource}.index", ['search' => 'no-match-12345']))->assertOk()->assertDontSee($data['name']);
    $this->get(route("{$resource}.edit", $record))->assertOk()->assertSee($data['name']);
    $updated = array_replace($data, ['name' => 'Updated audit', 'status' => 0]);
    $this->put(route("{$resource}.update", $record), $updated)->assertRedirect(route("{$resource}.index"))->assertSessionHasNoErrors();
    $this->assertDatabaseHas($record->getTable(), ['id' => $record->id, 'name' => 'Updated audit', 'status' => 0]);
    $this->get(route("{$resource}.index"))->assertOk()->assertSee('Updated audit')->assertSee('Inactivo');
    $this->delete(route("{$resource}.destroy", $record))->assertRedirect(route("{$resource}.index"));
    $this->assertDatabaseMissing($record->getTable(), ['id' => $record->id]);
})->with('module one catalogs');

test('module one validates required fields and status', function ($resource, $model, $data) {
    $role = Role::create(['name' => 'Administrador']);
    $this->actingAs(User::factory()->create(['role_id' => $role->id]));
    $this->post(route("{$resource}.store"), array_replace($data, ['name' => '', 'status' => 9]))->assertSessionHasErrors(['name', 'status']);
    expect($model::count())->toBe(0);
})->with('module one catalogs');

test('module one permits auditors to view catalogs but prevents edits', function ($resource, $model, $data, $roleName, $operation) {
    $role = Role::create(['name' => $roleName]);
    $record = $model::create($data);
    $this->actingAs(User::factory()->create(['role_id' => $role->id]));
    $method = match ($operation) {
        'store' => 'post', 'update' => 'put', 'destroy' => 'delete', default => 'get'
    };
    $parameters = in_array($operation, ['edit', 'update', 'destroy']) ? $record : [];
    $response = $this->{$method}(route("{$resource}.{$operation}", $parameters), $data);

    if ($roleName === 'Operador') {
        $response->assertRedirect(route('login'));
    } elseif ($roleName === 'Auditor' && $operation === 'index') {
        $response->assertOk();
    } else {
        $response->assertForbidden();
    }
})->with('module one catalogs')->with(['Operador', 'Auditor'])->with(['index', 'create', 'store', 'edit', 'update', 'destroy']);

test('module one redirects guests to login', function ($resource, $model, $data) {
    $this->get(route("{$resource}.index"))->assertRedirect(route('login'));
})->with('module one catalogs');
