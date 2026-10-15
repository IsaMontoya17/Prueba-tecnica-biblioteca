<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrestamoRequest;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $prestamos = Prestamo::with(['libro', 'usuario'])
            ->when($request->estado, fn ($q, $v) => $q->where('estado', $v))
            ->when($request->usuario_id, fn ($q, $v) => $q->where('usuario_id', $v))
            ->when($request->libro_id, fn ($q, $v) => $q->where('libro_id', $v))
            ->orderBy('id', 'desc')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Listado de préstamos',
            'data' => $prestamos->items(),
            'meta' => [
                'current_page' => $prestamos->currentPage(),
                'last_page' => $prestamos->lastPage(),
                'per_page' => $prestamos->perPage(),
                'total' => $prestamos->total(),
            ],
        ]);
    }

    public function store(StorePrestamoRequest $request): JsonResponse
    {
        $usuario = Usuario::findOrFail($request->usuario_id);

        if ($usuario->estado !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no está habilitado para solicitar préstamos.',
            ], 422);
        }

        $prestamosActivos = Prestamo::where('usuario_id', $usuario->id)
            ->whereIn('estado', ['activo', 'vencido'])
            ->count();

        if ($prestamosActivos >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya ha alcanzado el límite máximo de 3 préstamos activos.',
            ], 422);
        }

        $resultado = DB::transaction(function () use ($request) {
            $libro = Libro::where('id', $request->libro_id)->lockForUpdate()->first();

            if ($libro->stock_disponible <= 0) {
                return ['error' => 'No hay stock disponible para el libro solicitado.'];
            }

            $fechaPrestamo = $request->input('fecha_prestamo', now()->toDateString());
            $fechaEstimada = $request->input('fecha_devolucion_estimada', now()->addDays(15)->toDateString());

            $prestamo = Prestamo::create([
                'libro_id' => $libro->id,
                'usuario_id' => $request->usuario_id,
                'fecha_prestamo' => $fechaPrestamo,
                'fecha_devolucion_estimada' => $fechaEstimada,
                'fecha_devolucion_real' => null,
                'estado' => 'activo',
            ]);

            $libro->decrement('stock_disponible');

            return ['prestamo' => $prestamo];
        });

        if (isset($resultado['error'])) {
            return response()->json([
                'success' => false,
                'message' => $resultado['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Préstamo creado exitosamente',
            'data' => $resultado['prestamo']->load(['libro', 'usuario']),
        ], 201);
    }

    public function devolver(Prestamo $prestamo): JsonResponse
    {
        if ($prestamo->estado === 'devuelto') {
            return response()->json([
                'success' => false,
                'message' => 'Este préstamo ya se encuentra marcado como devuelto.',
            ], 422);
        }

        DB::transaction(function () use ($prestamo) {
            $prestamo->update([
                'estado' => 'devuelto',
                'fecha_devolucion_real' => now()->toDateString(),
            ]);

            $prestamo->libro()->increment('stock_disponible');
        });

        return response()->json([
            'success' => true,
            'message' => 'Préstamo marcado como devuelto exitosamente',
            'data' => $prestamo->fresh(['libro', 'usuario']),
        ]);
    }
}