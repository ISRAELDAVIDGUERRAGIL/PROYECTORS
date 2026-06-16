<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'id_cargo' => 'required|exists:cargos,id_cargo',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'fecha_ingreso' => 'required|date|before_or_equal:today',
            'salario' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['id_cargo'] = 'sometimes|exists:cargos,id_cargo';
            $rules['nombres'] = 'sometimes|string|max:100';
            $rules['apellidos'] = 'sometimes|string|max:100';
            $rules['fecha_nacimiento'] = 'sometimes|date|before:today';
            $rules['fecha_ingreso'] = 'sometimes|date|before_or_equal:today';
            $rules['salario'] = 'sometimes|numeric|min:0';
            $rules['estado'] = 'sometimes|in:activo,inactivo';
        }

        return $rules;
    }
}
