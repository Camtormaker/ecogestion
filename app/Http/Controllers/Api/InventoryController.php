<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Consultar inventario de todos los materiales.
     */
    public function index()
    {
        $inventory = $this->inventoryQuery()
            ->orderBy('m.name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Inventario consultado correctamente',
            'data' => $inventory
        ], 200);
    }

    /**
     * Consultar inventario de un material específico.
     */
    public function show($id)
    {
        $material = $this->inventoryQuery()
            ->where('m.id', $id)
            ->first();

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inventario consultado correctamente',
            'data' => $material
        ], 200);
    }

    /**
     * Construir consulta de inventario real.
     */
    private function inventoryQuery()
    {
        $collected = DB::table('collection_details')
            ->select('material_id')
            ->selectRaw('SUM(weight_kg) AS total_collected')
            ->groupBy('material_id');

        $delivered = DB::table('delivery_details')
            ->select('material_id')
            ->selectRaw('SUM(weight_kg) AS total_delivered')
            ->groupBy('material_id');

        return DB::table('materials as m')
            ->leftJoinSub(
                $collected,
                'collected',
                'm.id',
                '=',
                'collected.material_id'
            )
            ->leftJoinSub(
                $delivered,
                'delivered',
                'm.id',
                '=',
                'delivered.material_id'
            )
            ->select(
                'm.id as material_id',
                'm.name as material',
                'm.category',
                'm.unit'
            )
            ->selectRaw(
                'CAST(COALESCE(collected.total_collected, 0) AS DECIMAL(10,2)) AS total_collected'
            )
            ->selectRaw(
                'CAST(COALESCE(delivered.total_delivered, 0) AS DECIMAL(10,2)) AS total_delivered'
            )
            ->selectRaw(
                'CAST(
                    COALESCE(collected.total_collected, 0) -
                    COALESCE(delivered.total_delivered, 0)
                    AS DECIMAL(10,2)
                ) AS available'
            );
    }
}
