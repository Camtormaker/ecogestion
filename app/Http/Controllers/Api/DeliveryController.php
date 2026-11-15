<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Material;
use App\Models\RecyclingCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class DeliveryController extends Controller
{
    /**
     * Consultar todas las entregas.
     */
    public function index()
    {
        $deliveries = Delivery::with([
            'recyclingCenter',
            'user',
            'details.material'
        ])
        ->orderBy('delivery_date', 'desc')
        ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Entregas consultadas correctamente',
            'data' => $deliveries
        ], 200);
    }

    /**
     * Registrar una nueva entrega.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([

            'recycling_center_id' => [
                'required',
                'integer',
                Rule::exists('recycling_centers', 'id')
                    ->where(fn ($query) => $query->where('status', 1))
            ],

            'delivery_date' => [
                'required',
                'date'
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000'
            ],

            'receipt' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120'
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

        $receiptPath = null;

        try {

            // Guardar comprobante en Amazon S3
            $receiptPath = Storage::disk('s3')->putFile(
                'delivery-receipts',
                $request->file('receipt'),
                'private'
            );

            if (!$receiptPath) {
                throw new \RuntimeException(
                    'No se pudo almacenar el comprobante en S3.'
                );
            }

            // Registrar entrega y detalles en una transacción
            $delivery = DB::transaction(function () use (
                $validated,
                $request,
                $receiptPath
            ) {

                // Bloquear los materiales para evitar entregas simultáneas
                $materialIds = collect($validated['details'])
                    ->pluck('material_id')
                    ->sort()
                    ->values();

                $materials = Material::whereIn('id', $materialIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($validated['details'] as $detail) {

                    $materialId = $detail['material_id'];

                    $recollected = DB::table('collection_details')
                        ->join(
                            'collection_events',
                            'collection_details.collection_event_id',
                            '=',
                            'collection_events.id'
                        )
                        ->where('collection_details.material_id', $materialId)
                        ->sum('collection_details.weight_kg');

                    $delivered = DB::table('delivery_details')
                        ->where('material_id', $materialId)
                        ->sum('weight_kg');

                    // Trabajar en centésimas de kilogramo
                    // para evitar errores de comparación decimal.
                    $recollectedCents = (int) round(
                        (float) $recollected * 100
                    );

                    $deliveredCents = (int) round(
                        (float) $delivered * 100
                    );

                    $requestedCents = (int) round(
                        (float) $detail['weight_kg'] * 100
                    );

                    $availableCents = $recollectedCents - $deliveredCents;

                    if ($requestedCents > $availableCents) {

                        $materialName = $materials->get($materialId)?->name
                            ?? 'Material';

                        throw new \DomainException(
                            "Stock insuficiente para {$materialName}. " .
                            "Disponible: " .
                            number_format(max(0, $availableCents) / 100, 2) .
                            " kg."
                        );
                    }
                }

                // Crear encabezado de la entrega
                $delivery = Delivery::create([
                    'recycling_center_id' => $validated['recycling_center_id'],
                    'user_id' => $request->user()->id,
                    'delivery_date' => $validated['delivery_date'],
                    'receipt_url' => $receiptPath,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Crear detalles de los materiales entregados
                $delivery->details()->createMany(
                    $validated['details']
                );

                return $delivery;
            }, 3);

            $delivery->load([
                'recyclingCenter',
                'user',
                'details.material'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entrega registrada correctamente',
                'data' => $delivery
            ], 201);

        } catch (\DomainException $exception) {

            if ($receiptPath){
                Storage::disk('s3')->delete($receiptPath);
            }

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 422);

        } catch (Throwable $exception) {

            // Si falla la operación, eliminar el archivo subido
            if ($receiptPath) {
                Storage::disk('s3')->delete($receiptPath);
            }

            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo registrar la entrega. Verifica la configuración de almacenamiento y vuelve a intentarlo.'
            ], 500);
        }
    }

    /**
     * Consultar una entrega específica.
     */
    public function show($id)
    {
        $delivery = Delivery::with([
            'recyclingCenter',
            'user',
            'details.material'
        ])->find($id);

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrega consultada correctamente',
            'data' => $delivery
        ], 200);
    }
}
