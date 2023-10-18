<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $connection = "dinamic_connection";
    protected $table = 'clientes';
    protected $primaryKey = 'clienteid';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'nombre',
        'calle',
        'interior',
        'colonia',
        'telefono',
        'rfc',
        'observaciones',
        'fecha',
        'descuento',
        'xcalle',
        'xnumero',
        'xcolonia',
        'email',
        'sucursalid',
        'ultimacompra',
        'puntos',
        'v_puntos',
    ];
}
