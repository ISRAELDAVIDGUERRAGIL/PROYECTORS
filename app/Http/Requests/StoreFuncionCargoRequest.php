<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuncionCargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cargo' => 'required|exists:cargos,id_cargo',
            'descripcion_funcion' => 'required|string',
            'estado' => 'sometimes|in:activo,inactivo',
        ];
    }
}
