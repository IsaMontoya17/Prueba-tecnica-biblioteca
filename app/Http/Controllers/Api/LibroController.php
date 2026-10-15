<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLibroRequest;
use App\Http\Requests\UpdateLibroRequest;
use App\Models\Libro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibroController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $libros = Libro::with('autores')
            ->when($request->titulo, fn ($q, $v) => $q->where('titulo', 'like', "%{$v}%"))
            ->when($request->anio, fn ($q, $v) => $q->porAnio((int) $v))
            ->when($request->autor, fn ($q, $v) => $q->whereHas('autores', fn ($a) =>
                $a->where('nombre', 'like', "%{$v}%")->orWhere('apellido', 'like', "%{$v}%")))
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Listado de libros',
            'data' => $libros->items(),
            'meta' => [
                'current_page' => $libros->currentPage(),
                'last_page' => $libros->lastPage(),
                'per_page' => $libros->perPage(),
                'total' => $libros->total(),
            ],
        ]);
    }

    public function show(Libro $libro): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Libro encontrado',
            'data' => $libro->load('autores'),
        ]);
    }

    public function store(StoreLibroRequest $request): JsonResponse
    {
        $libro = DB::transaction(function () use ($request) {
            $libro = Libro::create($request->safe()->except('autores'));
            $libro->autores()->sync($this->autoresConOrden($request->autores));

            return $libro;
        });

        return response()->json([
            'success' => true,
            'message' => 'Libro creado',
            'data' => $libro->load('autores'),
        ], 201);
    }

    public function update(UpdateLibroRequest $request, Libro $libro): JsonResponse
    {
        DB::transaction(function () use ($request, $libro) {
            $libro->update($request->safe()->except('autores'));

            if ($request->has('autores')) {
                $libro->autores()->sync($this->autoresConOrden($request->autores));
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Libro actualizado',
            'data' => $libro->load('autores'),
        ]);
    }

    public function destroy(Libro $libro): JsonResponse
    {
        $libro->delete(); 

        return response()->json(['success' => true, 'message' => 'Libro eliminado']);
    }

    private function autoresConOrden(array $ids): array
    {
        $sync = [];
        foreach (array_values($ids) as $i => $id) {
            $sync[$id] = ['orden_autor' => $i + 1];
        }

        return $sync;
    }
}