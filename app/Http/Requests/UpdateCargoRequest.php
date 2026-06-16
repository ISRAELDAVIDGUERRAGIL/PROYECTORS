<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_cargo' => 'sometimes|string|max:100|unique:cargos,nombre_cargo,' . $this->cargo->id_cargo . ',id_cargo',
            'descripcion' => 'nullable|string',
        ];
    }
}
