<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    /** @use HasFactory<\Database\Factories\EmpleadoFactory> */
    use HasFactory;

    protected $primaryKey = 'id_empleado';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_cargo',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'fecha_ingreso',
        'salario',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'salario' => 'decimal:2',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }
}
