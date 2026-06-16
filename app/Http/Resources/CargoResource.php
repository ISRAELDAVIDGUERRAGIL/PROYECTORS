<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CargoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_cargo' => $this->id_cargo,
            'nombre_cargo' => $this->nombre_cargo,
            'descripcion' => $this->descripcion,
            'funciones' => FuncionCargoResource::collection(
                $this->whenLoaded('funcionesCargo')
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
