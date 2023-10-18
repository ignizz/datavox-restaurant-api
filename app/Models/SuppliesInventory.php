<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuppliesInventory extends Model
{
    use HasFactory;
    protected $connection = "dinamic_connection";
    protected $table = 'inv_insumos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'codigo',
        'descripcion',
        'presentacion',
        'ultprecio',
        'existencia',
        'conversion',
        'entrada',
        'salida',
        'notas',
        'real',
        'diferencia',
        'plantilla',
        'inicio',
    ];

    public function movements(){
        return $this->hasMany(MovementsInventory::class, "codigo", "codigo");
    }
}
