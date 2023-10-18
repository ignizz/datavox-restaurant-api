<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;
    protected $connection = "dinamic_connection";
    protected $table = 'platillos';
    protected $primaryKey = 'platillosid';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'platillo',
        'tipo',
        'medida',
        'limitemin',
        'limitemax',
        'precio',
        'nivel',
        'vigente',
        'cocina',
        'codigo',
        'orden',
        'clase',
        'precio2',
        'precio_1',
        'precio_2',
        'precio_3',
        'precio_4',
    ];
}
