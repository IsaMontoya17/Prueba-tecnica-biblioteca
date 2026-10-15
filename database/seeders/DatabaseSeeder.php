<?php

namespace Database\Seeders;

use App\Models\Autor;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@biblioteca.test',
            'password' => bcrypt('password'), 
        ]);
        $autores = Autor::factory(10)->create();

        Libro::factory(20)->create()->each(function (Libro $libro) use ($autores) {
            foreach ($autores->random(rand(1, 2))->values() as $i => $autor) {
                $libro->autores()->attach($autor->id, ['orden_autor' => $i + 1]);
            }
        });

        Libro::inRandomOrder()->take(2)->update(['stock_disponible' => 0]);

        Usuario::factory(13)->create();
        Usuario::factory(2)->create(['estado' => 'inactivo']);

        $prestadores = Usuario::where('estado', 'activo')->inRandomOrder()->take(10)->get();
        $estados = ['activo', 'activo', 'activo', 'activo', 'activo', 'activo',
                    'devuelto', 'devuelto', 'vencido', 'vencido'];

        foreach ($prestadores as $i => $usuario) {
            $libro = Libro::where('stock_disponible', '>', 0)->inRandomOrder()->first();
            $estado = $estados[$i];

            $fechaPrestamo = match ($estado) {
                'vencido' => now()->subDays(rand(20, 40)),
                'devuelto' => now()->subDays(rand(5, 14)),
                default => now()->subDays(rand(0, 10)),
            };

            Prestamo::create([
                'libro_id' => $libro->id,
                'usuario_id' => $usuario->id,
                'fecha_prestamo' => $fechaPrestamo->toDateString(),
                'fecha_devolucion_estimada' => $fechaPrestamo->copy()->addDays(15)->toDateString(),
                'fecha_devolucion_real' => $estado === 'devuelto'
                    ? $fechaPrestamo->copy()->addDays(rand(3, 10))->toDateString()
                    : null,
                'estado' => $estado,
            ]);

            if ($estado !== 'devuelto') {
                $libro->decrement('stock_disponible');
            }
        }
    }
}