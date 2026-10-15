<?php

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'total_libros' => Libro::count(),
        'libros_disponibles' => Libro::where('stock_disponible', '>', 0)->count(),
        'prestamos_activos' => Prestamo::whereIn('estado', ['activo', 'vencido'])->count(),
        'usuarios_activos' => Usuario::where('estado', 'activo')->count(),
    ];

    $usuarios = Usuario::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre']);
    $libros = Libro::where('stock_disponible', '>', 0)->orderBy('titulo')->get(['id', 'titulo', 'stock_disponible']);

    return view('biblioteca', compact('stats', 'usuarios', 'libros'));
});
