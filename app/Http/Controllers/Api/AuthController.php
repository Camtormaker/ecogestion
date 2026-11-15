<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Iniciar sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('status', 1)
            ->first();

        if (! $user || ! Hash::check(
            $credentials['password'],
            $user->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas o usuario inactivo',
            ], 401);
        }

        $user->loadMissing('role');

        if (! $user->isAdministrator() && ! $user->isOperator() && ! $user->isAuditor()) {
            return response()->json([
                'success' => false,
                'message' => 'Tu rol no tiene acceso a EcoGestión React',
            ], 403);
        }

        $token = $user->createToken('ecogestion-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión correcto',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    // Consultar usuario autenticado
    public function me(Request $request)
    {
        $user = $request->user();

        if (! $user->status) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está inactiva',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $request->user()->load('role'),
        ], 200);
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente',
        ], 200);
    }
}
