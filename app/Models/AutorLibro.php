<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AutorLibro extends Pivot
{
    protected $table = 'autor_libro';

    public $incrementing = false;

    protected $fillable = ['autor_id', 'libro_id', 'orden_autor'];

    protected $casts = [
        'orden_autor' => 'integer',
    ];
}