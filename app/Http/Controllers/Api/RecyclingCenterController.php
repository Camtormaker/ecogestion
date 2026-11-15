<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecyclingCenter;
use Illuminate\Http\Request;

class RecyclingCenterController extends Controller
{
    // Consultar centros de reciclaje
    public function index(Request $request)
    {
        $centers = RecyclingCenter::query()
            ->where('status', 1)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Centros de reciclaje consultados correctamente',
            'data' => $centers
        ], 200);
    }

    // Consultar un centro específico
    public function show($id)
    {
        $center = RecyclingCenter::where('status', 1)
            ->find($id);

        if (!$center) {
            return response()->json([
                'success' => false,
                'message' => 'Centro de reciclaje no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Centro de reciclaje encontrado',
            'data' => $center
        ], 200);
    }
}
