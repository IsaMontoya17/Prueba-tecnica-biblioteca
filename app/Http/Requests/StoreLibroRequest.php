<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLibroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:libros,isbn',
            'anio_publicacion' => 'required|integer|min:1000|max:' . date('Y'),
            'numero_paginas' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'stock_disponible' => 'required|integer|min:0',
            'autores' => 'required|array|min:1',
            'autores.*' => 'integer|exists:autores,id',
        ];
    }
}
