<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFuncionCargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cargo' => 'sometimes|exists:cargos,id_cargo',
            'descripcion_funcion' => 'sometimes|string',
            'estado' => 'sometimes|in:activo,inactivo',
        ];
    }
}
