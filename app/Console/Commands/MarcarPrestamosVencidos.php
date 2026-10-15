<?php

namespace App\Console\Commands;

use App\Models\Prestamo;
use Illuminate\Console\Command;

class MarcarPrestamosVencidos extends Command
{
    protected $signature = 'biblioteca:marcar-vencidos';
    protected $description = 'Marca como vencidos los préstamos activos con fecha estimada superada';

    public function handle(): int
    {
        $this->info('Analizando préstamos activos...');

        $afectados = Prestamo::where('estado', 'activo')
            ->whereDate('fecha_devolucion_estimada', '<', now()->toDateString())
            ->update(['estado' => 'vencido']);

        $this->info("Se marcaron {$afectados} préstamo(s) como vencido(s).");

        return Command::SUCCESS;
    }
}