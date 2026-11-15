<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CollectionPoint;
use Illuminate\Http\Request;

class CollectionPointController extends Controller
{
    public function index(Request $request)
    {
        $points = CollectionPoint::query()
            ->where('status', 1)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Puntos de recolección consultados correctamente',
            'data' => $points
        ], 200);
    }

    public function show($id)
    {
        $point = CollectionPoint::where('status', 1)->find($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Punto de recolección no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Punto de recolección encontrado',
            'data' => $point
        ], 200);
    }
}
