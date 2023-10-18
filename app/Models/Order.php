<?php

namespace App\Models;

use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $connection = "dinamic_connection";
    protected $table = 'orden';
    protected $primaryKey = 'ordenid';
    public $timestamps = false;

    // Define los nombres de las columnas que son asignables en masa (Mass Assignment)
    protected $fillable = [
        'clientesid',
        'repartidorid',
        'horainicio',
        'horapreparacion',
        'horaenvio',
        'horafinal',
        'status',
        'servicio',
        'cajeraid',
        'estacion',
        'tipocambio',
        'abono',
        'platillos',
        'cancelaid',
        'horacancela',
        'modificacionid',
        'horamodificacion',
        'observacion',
        'descuento',
        'cambio',
        'descuentop',
        'descuentoe',
        'corte',
        'corterepartidor',
        'numerocontrol',
        'horareg',
        'factura',
        'cortecaja',
        'sucursalid',
        'clientenombre',
        'ticket',
        'npersonas',
        'meseroid',
        'clave',
        'cobraid',
    ];
    /**
     * constants for current table statuses
     * @created 2023-08-16
     * @author Kareem Lorenzana
     */
    const STATUS_CANCELED=7;
    const CANCELA_ID=1;

    //todo para cancelar la orden se actualiza el campo status a STATUS_CANCELED y el campo cancela_id igual a 1, y hora_cancela=current_timestamp, comentario de cancelacion = observaciones

    //todo filtro: al buscar la orden buscas por el campo hora_inicio y el campo clave sea igual al numero de orden que está pidiendo.

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'ordenid', 'ordenid')->with("dishName");
    }
    // Relación con la tabla 'clientes'
    public function customerName()
    {
        return $this->belongsTo(Customer::class, 'clientesid', 'clienteid')->select(["clienteid", "nombre"]);
    }

    // Relación con la tabla 'servicio'
    public function service()
    {
        return $this->belongsTo(Service::class, 'servicio', 'servicioid');
    }

    // Relación con la tabla 'empleados'
    public function employeeName()
    {
        return $this->belongsTo(Employee::class, 'cajeraid', 'empleadoid')->select(["empleadoid", "nombre"]);
    }
}
