<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    protected $table = 'prestamos';

    protected $fillable = [
        'libro_id', 'usuario_id', 'fecha_prestamo',
        'fecha_devolucion_estimada', 'fecha_devolucion_real', 'estado',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_devolucion_estimada' => 'date',
        'fecha_devolucion_real' => 'date',
    ];

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}