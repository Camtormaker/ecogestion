<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Cargamos la relación 'role' con eager loading para evitar el problema de consultas N+1
        $users = User::with('role')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })->paginate(10);

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|integer|in:0,1',
        ]);

        // Encriptamos la contraseña de forma segura
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('message', 'Usuario registrado con éxito.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|integer|in:0,1',
        ]);

        // Si el usuario no escribió una nueva contraseña, la quitamos del arreglo para no machacar la actual
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('message', 'Usuario actualizado con éxito.');
    }

    public function destroy(User $user)
    {
        // Medida de seguridad básica: impedir que un usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('message', 'Usuario eliminado correctamente.');
    }
}
