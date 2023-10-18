<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementsInventory extends Model
{
    use HasFactory;

    protected $connection = "dinamic_connection";
    protected $table = 'inv_movimientos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const MOVEMENT_IN="E";
    const MOVEMENT_OUT="C";

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'movimiento',
        'cantidad',
        'precio',
        'preciounit',
        'usuario',
        'fechareg',
        'codigo',
        'tipo_movimiento',
        'notas',
    ];

    // Relación con la tabla 'inv_insumos'
    public function supply()
    {
        return $this->belongsTo(SuppliesInventory::class, 'codigo', 'codigo');
    }
}
