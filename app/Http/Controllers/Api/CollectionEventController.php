<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CollectionEvent;
use App\Models\CollectionPoint;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CollectionEventController extends Controller
{
    /**
     * Consultar jornadas de recolección.
     */
    public function index()
    {
        $events = CollectionEvent::with([
            'collectionPoint',
            'user',
            'details.material'
        ])
        ->orderBy('event_date', 'desc')
        ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Jornadas consultadas correctamente',
            'data' => $events
        ], 200);
    }

    /**
     * Registrar una nueva jornada de recolección.
     */
    public function store(Request $request)
    {
        // Validar la información recibida
        $validated = $request->validate([

            'collection_point_id' => [
                'required',
                'integer',
                Rule::exists('collection_points', 'id')
                    ->where(fn ($query) => $query->where('status', 1))
            ],

            'event_date' => [
                'required',
                'date'
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000'
            ],

            'details' => [
                'required',
                'array',
                'min:1'
            ],

            'details.*.material_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('materials', 'id')
                    ->where(fn ($query) => $query->where('status', 1))
            ],

            'details.*.weight_kg' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2'
            ],

        ]);

        // Guardar la jornada y sus detalles en una sola transacción
        $event = DB::transaction(function () use ($validated, $request) {

            $event = CollectionEvent::create([
                'collection_point_id' => $validated['collection_point_id'],
                'user_id' => $request->user()->id,
                'event_date' => $validated['event_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $event->details()->createMany(
                $validated['details']
            );

            return $event;
        });

        // Devolver la jornada registrada
        $event->load([
            'collectionPoint',
            'user',
            'details.material'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jornada de recolección registrada correctamente',
            'data' => $event
        ], 201);
    }

    /**
     * Consultar una jornada específica.
     */
    public function show($id)
    {
        $event = CollectionEvent::with([
            'collectionPoint',
            'user',
            'details.material'
        ])->find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Jornada de recolección no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jornada consultada correctamente',
            'data' => $event
        ], 200);
    }
}
