<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_cargo' => 'required|string|max:100|unique:cargos,nombre_cargo',
            'descripcion' => 'nullable|string',
        ];
    }
}
