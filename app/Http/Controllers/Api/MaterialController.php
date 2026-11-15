<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Listar materiales registrados.
     */
    public function index(Request $request)
    {
        $materials = Material::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Materiales consultados correctamente',
            'data' => $materials
        ], 200);
    }

    /**
     * Consultar un material específico.
     */
    public function show(string $id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $material
        ], 200);
    }
}