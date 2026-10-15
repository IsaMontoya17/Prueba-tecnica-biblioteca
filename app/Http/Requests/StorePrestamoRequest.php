<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrestamoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libro_id' => ['required', 'integer', 'exists:libros,id'],
            'usuario_id' => ['required', 'integer', 'exists:usuarios,id'],
            'fecha_prestamo' => ['nullable', 'date'],
            'fecha_devolucion_estimada' => ['nullable', 'date', 'after_or_equal:fecha_prestamo'],
        ];
    }

    public function messages(): array
    {
        return [
            'libro_id.required' => 'El libro es obligatorio.',
            'libro_id.exists' => 'El libro seleccionado no existe.',
            'usuario_id.required' => 'El usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'fecha_devolucion_estimada.after_or_equal' => 'La fecha estimada debe ser posterior o igual a la fecha de préstamo.',
        ];
    }
}