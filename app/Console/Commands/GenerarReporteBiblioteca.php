<?php

namespace App\Console\Commands;

use App\Models\Libro;
use App\Models\Usuario;
use Illuminate\Console\Command;

class GenerarReporteBiblioteca extends Command
{
    protected $signature = 'biblioteca:reporte';
    protected $description = 'Genera un reporte consolidado del estado de la biblioteca';

    public function handle(): int
    {
        $this->newLine();
        $this->info('REPORTE DE BIBLIOTECA');

        $this->newLine();
        $this->warn('TOP 5 LIBROS MÁS PRESTADOS');
        $librosMasPrestados = Libro::withCount('prestamos')
            ->orderByDesc('prestamos_count')
            ->take(5)
            ->get(['id', 'titulo', 'isbn']);

        $this->table(
            ['ID', 'Título', 'ISBN', 'Total Préstamos'],
            $librosMasPrestados->map(fn ($l) => [
                $l->id,
                $l->titulo,
                $l->isbn,
                $l->prestamos_count,
            ])
        );

        $this->newLine();
        $this->warn('USUARIOS CON PRÉSTAMOS VENCIDOS');
        $usuariosVencidos = Usuario::whereHas('prestamos', fn ($q) => $q->where('estado', 'vencido'))
            ->with(['prestamos' => fn ($q) => $q->where('estado', 'vencido')->with('libro')])
            ->get();

        if ($usuariosVencidos->isEmpty()) {
            $this->line('No hay usuarios con préstamos vencidos.');
        } else {
            $filasUsuarios = [];
            foreach ($usuariosVencidos as $u) {
                foreach ($u->prestamos as $p) {
                    $filasUsuarios[] = [
                        $u->id,
                        $u->nombre,
                        $u->email,
                        $p->libro->titulo ?? 'N/A',
                        $p->fecha_devolucion_estimada?->format('Y-m-d'),
                    ];
                }
            }
            $this->table(
                ['ID Usuario', 'Nombre', 'Email', 'Libro Adeudado', 'Fecha Estimada'],
                $filasUsuarios
            );
        }

        $this->newLine();
        $this->warn('LIBROS SIN STOCK DISPONIBLE');
        $librosSinStock = Libro::where('stock_disponible', '<=', 0)
            ->get(['id', 'titulo', 'isbn', 'stock_disponible']);

        if ($librosSinStock->isEmpty()) {
            $this->line('Todos los libros cuentan con existencias en stock.');
        } else {
            $this->table(
                ['ID', 'Título', 'ISBN', 'Stock'],
                $librosSinStock->map(fn ($l) => [
                    $l->id,
                    $l->titulo,
                    $l->isbn,
                    $l->stock_disponible,
                ])
            );
        }

        $this->newLine();
        $this->info('Reporte finalizado exitosamente.');

        return Command::SUCCESS;
    }
}