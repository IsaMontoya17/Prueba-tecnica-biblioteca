<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLibroRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'sometimes|string|max:255',
            'isbn' => ['sometimes', 'string', 'max:20', Rule::unique('libros', 'isbn')->ignore($this->route('libro'))],
            'anio_publicacion' => 'sometimes|integer|min:1000|max:' . date('Y'),
            'numero_paginas' => 'sometimes|integer|min:1',
            'descripcion' => 'nullable|string',
            'stock_disponible' => 'sometimes|integer|min:0',
            'autores' => 'sometimes|array|min:1',
            'autores.*' => 'integer|exists:autores,id',
        ];
    }
}
