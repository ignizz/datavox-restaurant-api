<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $connection = "pgsql";
    protected $table = 'empleados';
    protected $primaryKey = 'empleadoid';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'seguro',
        'licencia',
        'numeroempleado',
        'clave',
        'puesto',
        'conectado',
        'repartidor',
        'sucursalid',
        'status',
        'efectivor',
        'efectivoc',
        'maxefectivo',
        'fechaconexion',
        'efectivomax',
        'activar_efectivomax',
        'nivel',
        'huellaBit',
    ];
    const STATUS_ALTA="ALTA";
}
