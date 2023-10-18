<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $connection = "dinamic_connection";
    protected $table = 'servicio';
    protected $primaryKey = 'servicioid';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'servicio',
        'tipo',
        'imprimir',
        'imprimir2',
    ];
}
