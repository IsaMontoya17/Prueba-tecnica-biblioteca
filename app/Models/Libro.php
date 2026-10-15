<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Libro extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'libros';

    protected $fillable = [
        'titulo', 'isbn', 'anio_publicacion',
        'numero_paginas', 'descripcion', 'stock_disponible',
    ];

    protected $casts = [
        'anio_publicacion' => 'integer',
        'numero_paginas' => 'integer',
        'stock_disponible' => 'integer',
    ];

    public function setTituloAttribute(string $value): void
    {
        $this->attributes['titulo'] = trim($value);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_libro', 'libro_id', 'autor_id')
            ->using(AutorLibro::class)
            ->withPivot('orden_autor')
            ->withTimestamps()
            ->orderBy('autor_libro.orden_autor');
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('stock_disponible', '>', 0);
    }

    public function scopePorAnio(Builder $query, int $anio): Builder
    {
        return $query->where('anio_publicacion', $anio);
    }

    public function scopePorAutor(Builder $query, int $autorId): Builder
    {
        return $query->whereHas('autores', fn ($q) => $q->where('autores.id', $autorId));
    }
}