<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Autor extends Model
{
    use HasFactory;
    protected $table = 'autores';

    protected $fillable = ['nombre', 'apellido', 'fecha_nacimiento', 'nacionalidad', 'biografia'];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function libros(): BelongsToMany
    {
        return $this->belongsToMany(Libro::class, 'autor_libro', 'autor_id', 'libro_id')
            ->using(AutorLibro::class)
            ->withPivot('orden_autor')
            ->withTimestamps();
    }
}