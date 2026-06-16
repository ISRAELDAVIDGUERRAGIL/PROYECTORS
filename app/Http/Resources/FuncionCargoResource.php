<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuncionCargoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_funcion' => $this->id_funcion,
            'id_cargo' => $this->id_cargo,
            'descripcion_funcion' => $this->descripcion_funcion,
            'estado' => $this->estado,
            'cargo' => new CargoResource($this->whenLoaded('cargo')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
