<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_empleado' => $this->id_empleado,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'nombre_completo' => $this->nombres . ' ' . $this->apellidos,
            'fecha_nacimiento' => $this->fecha_nacimiento->format('Y-m-d'),
            'fecha_ingreso' => $this->fecha_ingreso->format('Y-m-d'),
            'salario' => (float) $this->salario,
            'estado' => $this->estado,
            'creado_en' => $this->created_at?->toDateTimeString(),
            'actualizado_en' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
