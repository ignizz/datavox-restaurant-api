<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $table = 'mesas'; // Nombre de la tabla

    public $timestamps = false; // Deshabilitar marcas de tiempo
    protected $primaryKey = "mesaid";
    protected $fillable = [
        'nombre',
    ];
}
