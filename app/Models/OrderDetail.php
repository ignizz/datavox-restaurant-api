<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $connection = "dinamic_connection";
    protected $table = 'ordendetalles';
    protected $primaryKey = 'ordendetalleid';
    public $timestamps = false;

    /**
     * constants for current table statuses
     * @created 2023-08-16
     * @author Kareem Lorenzana
     */
    const STATUS_CANCELED = 2, CANCELA_ID=1;
    //todo campos afectados status=STATUS_CANCELED, cancela_id=1, cancela_hora=current_timestamp comentario de cancelacion = observaciones

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'ordenid',
        'platilloid',
        'cantidad',
        'observaciones',
        'importeunitario',
        'cocina',
        'horapreparacion',
        'sincocinar',
        'status',
        'promocionid',
        'sucursalid',
        'precioventa',
        'ingredientes',
        'cancelaid',
        'cancelahora',
        'costo',
    ];

    // Relación con la tabla 'orden'
    public function order()
    {
        return $this->belongsTo(Order::class, 'ordenid', 'ordenid');
    }

    // Relación con la tabla 'platillos'
    public function dishName()
    {
        return $this->belongsTo(Dish::class, 'platilloid', 'platillosid')->select(["platillosid", "platillo"]);
    }
}
