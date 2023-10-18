<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;
    const STATUS_CANCEL = 0;
    protected $connection = "dinamic_connection";
    protected $table = 'ordenpagos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'ordenid',
        'importe',
        'status',
        'fecha',
        'moneda',
        'empleadoid',
        'corte',
    ];

    // Relación con la tabla 'orden'
    public function order()
    {
        return $this->belongsTo(Order::class, 'ordenid', 'ordenid');
    }

    // Relación con la tabla 'empleados'
    public function emproyee()
    {
        return $this->belongsTo(Employee::class, 'empleadoid', 'empleadoid');
    }
}
