<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EmpleadoRequest;
use App\Http\Resources\EmpleadoResource;
use App\Models\Empleado;
use Illuminate\Http\JsonResponse;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $empleados = Empleado::all();
        return response()->json([
            'success' => true,
            'data' => EmpleadoResource::collection($empleados),
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $empleado = Empleado::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Empleado creado exitosamente',
            'data' => new EmpleadoResource($empleado),
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $empleado = Empleado::find($id);
        if (!$empleado) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado'], 404);
        }
        return response()->json(['success' => true, 'data' => new EmpleadoResource($empleado)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $empleado = Empleado::find($id);
        if (!$empleado) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado'], 404);
        }
        $empleado->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Empleado actualizado exitosamente',
            'data' => new EmpleadoResource($empleado),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $empleado = Empleado::find($id);
        if (!$empleado) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado'], 404);
        }
        $empleado->delete();
        return response()->json(['success' => true, 'message' => 'Empleado eliminado exitosamente']);
    }
}
