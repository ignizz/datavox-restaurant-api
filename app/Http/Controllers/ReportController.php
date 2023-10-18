<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Connection;
use App\Mail\SendCashProof;
use Illuminate\Support\Str;
use App\Exports\Top10Export;
use Illuminate\Http\Request;
use App\Models\CashProofMail;
use App\Exports\CollectionExport;
use App\Exports\CustomerSaleDate;
use App\Exports\AccontAuditExport;
use App\Exports\SalesByHourExport;
use App\Exports\SalesByTypeExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exports\LastSaleDateExport;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssistantReportExport;
use Illuminate\Support\Facades\Process;
use App\Exports\CancelAndDiscountExport;
use App\Http\Requests\Report\Top10Request;
use App\Http\Requests\Report\SalesByHourRequest;
use App\Http\Requests\Report\SalesByTypeRequest;
use App\Http\Requests\Report\AccountAuditRequest;
use App\Http\Requests\Report\CancelAndDiscountRequest;
use App\Http\Requests\Report\CustomerLastSaleDateRequest;

class ReportController extends Controller
{

    public function __construct()
    {}

    public function salesDay(Connection $connection,Request $request)
    {
        if(auth()->check()){
            $user = auth()->user();
        }else{
            $user = User::find(1);
        }

        $dataResponse = null;
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $request->fechaFin? date("Y-m-d", (strtotime($request->fechaFin. ' +1 day'))) : date("Y-m-d", (strtotime(date("Y-m-d"). ' +1 day')));
        $lastDate = $lastDate.' '.'04:00:00';
        $objectResponse = null;

        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $result = DB::connection("dinamic_connection")->table("orden")->select(DB::raw("sum(importeunitario) as total "))->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")->whereRaw("orden.status in(0,1,2,3,4,5,6,8) and  ordendetalles.status=1 and   orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate' ")->get();
            $latestSale = DB::connection("dinamic_connection")->table("orden")->select(DB::raw("orden.horainicio"))->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")->whereRaw("orden.status in(0,1,2,3,4,5,6,8) and  ordendetalles.status=1 and   orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate' ")->orderBy("orden.horainicio", "desc")->first();

            if($result && $latestSale){
                $result = $result->first();
                $result = $result->total?  $result->total : 0;
                $latestSale = $latestSale->horainicio? $latestSale->horainicio: '';
                $dataResponse = (object)[
                    "branch" => $connection->name,
                    "total_sales" => $result,
                    "latest_sale" => date("H:i:s", strtotime($latestSale))
                ];
            }else{
                $dataResponse = (object)[
                    "branch" => $connection->name,
                    "total_sales" => "0.00",
                    "latest_sale" => "sin datos"
                ];
            }
        }catch(\Exception $e){
            $dataResponse = (object)[
                "branch" => $connection->name,
                "total_sales" => "sin conexion",
                "latest_sale" => "sin conexion"
            ];
        }
        return response()->json(["data" => $dataResponse, "dates" => (object)["firstDate" => $firstDate, "lastDate"=> $lastDate]], parent::SUCCESS_RESPONSE);
    }

    public function exportSalesDay(Request $request)
    {
        $data = new Collection($request->data);
        $name = "sales-date.xlsx";
        Excel::store(new CollectionExport($data), $name, 'public');
        return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
    }

    public function accountAudit(Connection $connection, AccountAuditRequest $request)
    {

        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $request->fechaFin? date("Y-m-d", (strtotime($request->fechaFin. ' +1 day'))) : date("Y-m-d", (strtotime(date("Y-m-d"). ' +1 day')));
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $result = DB::connection("dinamic_connection")->table("orden")
            ->join("servicio", "orden.servicio", "=", "servicio.servicioid")
            ->join("ordendetalles","orden.ordenid" , "=","ordendetalles.ordenid")
            ->join("empleados", "orden.cajeraid", "=", "empleados.empleadoid")
            ->select("orden.clave","orden.ordenid","servicio.servicio", DB::raw("EXTRACT(DAY FROM orden.horainicio) as dia"), DB::raw("to_char(orden.horainicio, 'hh24:mi') as Hora"), "empleados.nombre as mesero", DB::raw("sum(ordendetalles.importeunitario) as venta"))
            ->whereRaw("orden.ordenid=ordendetalles.ordenid and orden.servicio=servicio.servicioid and orden.cajeraid=empleados.empleadoid and orden.status in(0,1,2,3,4,5,6,8) and ordendetalles.status=1")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->groupBy("orden.clave", "orden.ordenid", "servicio.servicio", "orden.horainicio", "empleados.nombre")->orderBy("orden.clave", "asc")->get();
        if($request->excel){
            $name = "auditoria-cuentas".$connection->name.date("HHss").".xlsx";
            Excel::store(new AccontAuditExport($result), $name, 'public');
            return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
        }
        return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }

    }
    public function top10(Connection $connection, Top10Request $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $request->fechaFin? date("Y-m-d", (strtotime($request->fechaFin. ' +1 day'))) : date("Y-m-d", (strtotime(date("Y-m-d"). ' +1 day')));
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $result = DB::connection("dinamic_connection")->table("ordendetalles")
            ->join("platillos", "ordendetalles.platilloid", "=", "platillos.platillosid")
            ->join("orden", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->whereRaw("orden.status in(0,1,2,3,4,5,6,8) and ordendetalles.status=1")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("count(ordendetalles.platilloid) as Cantidad"),DB::raw("sum(ordendetalles.importeunitario) as Venta"), "tipo", "platillo as Platillos")
            ->groupBy("tipo", "platillo")->orderBy("venta", "desc")->distinct()->get();


        if($request->excel){
            $name = "top-10".$connection->name.date("HHss").".xlsx";
            Excel::store(new Top10Export($result), $name, 'public');
            return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
        }
        return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            dd($e->getMessage());
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }
    public function salesByType(Connection $connection, SalesByTypeRequest $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $request->fechaFin? date("Y-m-d", (strtotime($request->fechaFin. ' +1 day'))) : date("Y-m-d", (strtotime(date("Y-m-d"). ' +1 day')));
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $subquery = "(select count(o2.servicio) from orden o2, servicio s2 where o2.servicio=s2.servicioid and servicio.servicio=s2.servicio and  o2.status in(0,1,2,3,4,5,6,8)  and   o2.horainicio > '$firstDate' and o2.horainicio < '$lastDate' ) as cuantos";
            $result =
            $result = DB::connection("dinamic_connection")->table("orden")
            ->join("ordendetalles", "orden.ordenid", "=", "ordendetalles.ordenid")
            ->join("servicio", "orden.servicio", "=", "servicio.servicioid")
            ->whereRaw("orden.status in(0,1,2,3,4,5,6,8) and ordendetalles.status=1")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("sum(ordendetalles.importeunitario) as venta"),"servicio.servicio", DB::raw($subquery))
            ->groupBy("servicio.servicio")->get();

        if($request->excel){
            $name = "ventas-por-tipo".$connection->name.date("HHss").".xlsx";
            Excel::store(new SalesByTypeExport($result), $name, 'public');
            return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
        }
        return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            dd($e->getMessage());
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }
    public function cancelAndDiscounts(Connection $connection, CancelAndDiscountRequest $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $request->fechaFin? date("Y-m-d", (strtotime($request->fechaFin. ' +1 day'))) : date("Y-m-d", (strtotime(date("Y-m-d"). ' +1 day')));
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $resultOrdersDiscount = DB::connection("dinamic_connection")->table("ordendetalles")
            ->join("orden", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->join("platillos", "ordendetalles.platilloid", "=", "platillos.platillosid")
            ->join("servicio", "orden.servicio", "=", "servicio.servicioid")
            ->join("empleados", "ordendetalles.cancelaid", "=", "empleados.empleadoid")
            ->whereRaw("ordendetalles.status=1 and platillos.platillosid=0")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("servicio.servicio"), DB::raw("EXTRACT(DAY FROM orden.horainicio) as dia"), DB::raw("to_char(orden.horainicio, 'HH24:MI') as Hora"), DB::raw("to_char(ordendetalles.cancelahora, 'HH24:MI') as HC"), DB::raw("importeunitario as importe"), "empleados.nombre as autorizo", "orden.clave", "orden.ordenid", "ordendetalles.observaciones")->orderBy("orden.clave", "asc")->get();

            $resultOrdersCancel = DB::connection("dinamic_connection")->table("ordendetalles")
            ->join("orden", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->join("servicio", "orden.servicio", "=", "servicio.servicioid")
            ->join("empleados", "orden.cancelaid", "=", "empleados.empleadoid")
            ->whereRaw("orden.status=7")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("servicio.servicio"), DB::raw("EXTRACT(DAY FROM orden.horainicio) as dia"), DB::raw("to_char(orden.horainicio, 'HH24:MI') as Hora"), DB::raw(" to_char(orden.horacancela, 'HH24:MI')as HC"), DB::raw("sum(importeunitario) as importe"), "empleados.nombre as autorizo", "orden.clave", "orden.ordenid", "orden.observacion as observaciones")
            ->groupBy("servicio.servicio", "orden.horainicio", "orden.horacancela", "empleados.nombre", "orden.clave", "orden.ordenid")->orderBy("orden.clave", "asc")->get();

            $resultDishesCancel = DB::connection("dinamic_connection")->table("ordendetalles")
            ->join("orden", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->join("platillos", "ordendetalles.platilloid", "=", "platillos.platillosid")
            ->join("empleados", "ordendetalles.cancelaid", "=", "empleados.empleadoid")
            ->whereRaw("ordendetalles.status in(2)")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("EXTRACT(DAY FROM orden.horainicio) as dia"), DB::raw("to_char(orden.horainicio, 'HH24:MI') as Hora"), DB::raw("to_char(ordendetalles.cancelahora, 'HH24:MI')as HC"), "platillos.platillo as descripcion","precio", "empleados.nombre as autorizo", "orden.clave", "orden.ordenid",  "ordendetalles.observaciones")->orderBy("orden.clave", "asc")->get();

            $result = (object)[
                "orders_discount" => $resultOrdersDiscount,
                "orders_cancel" => $resultOrdersCancel,
                "dishes_cancel" => $resultDishesCancel,
            ];

        if($request->excel){
            $name = "Cancelaciones y Descuentos - ".$connection->name.date("HHss").".xlsx";
            Excel::store(new CancelAndDiscountExport($result), $name, 'public');
            return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
        }
        return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            dd($e->getMessage());
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-06-23
     * @description send sales by hour on selected date
     */
    public function salesByHour(Connection $connection, SalesByHourRequest $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $lastDate = date("Y-m-d", (strtotime($firstDate.' +1 day')));
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);

        try{
            $dataQuery = DB::connection("dinamic_connection")->table("orden")
            ->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->whereRaw("ordendetalles.status=1 and  orden.status in (0,1,2,3,4,5,6,8)")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("(EXTRACT(HOUR FROM orden.horainicio)) as hora"), DB::raw("sum(ordendetalles.importeunitario) as importe"))->groupBy("orden.horainicio")->get();
            $result = [];
            for($i=7; $i<22; $i++){
                $salePerHour = 0;
                $quantity = 0;
                $next = $i+1;
                $saleTime = $i < 10?( $i == 9? "09-10" : "0$i-0$next") : "$i-$next";
                foreach($dataQuery as $data){
                    if($i == intval($data->hora)){
                        $salePerHour = $salePerHour + floatval($data->importe);
                        $quantity++;
                    }
                }
                if($quantity > 0){

                    $result[] = (object)[
                        "hora" => $saleTime,
                        "cantidad" => $quantity,
                        "venta" => $salePerHour
                    ];
                }
            }
            $totalSales = 0;
            $countSales = 0;
            foreach($dataQuery as $data){
                $totalSales = $totalSales + floatval($data->importe);
                $countSales++;
            }
            $result = collect($result)->map(function (object $data) use($totalSales, $countSales){
                $data->venta_porcentaje = $data->venta > 0? (floatval($data->venta) /floatval($totalSales))* 100 : 0;
                $data->total_sales = $totalSales;
                $data->count_sales = $countSales;
                return $data;
            });

            if($request->excel){
                #todo
                $name = "salesByHour - ".$connection->name.date("HHss").".xlsx";
                Excel::store(new SalesByHourExport($result), $name, 'public');
                return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
            }
            return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-07-01
     * @description clientes fecha ultima compra
     */
    public function lastSaleDate(Connection $connection, CustomerLastSaleDateRequest $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $lastDate = $request->fechaFin? $request->fechaFin : date("Y-m-d");
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $result = DB::connection("dinamic_connection")->table("clientes")
            ->join("ncalles", "clientes.xcalle", "=", "ncalles.calleid")
            ->join("ncolonias", "clientes.xcolonia", "=", "ncolonias.colid")
            ->whereRaw("clientes.ultimacompra >= '$firstDate' and clientes.ultimacompra <= '$lastDate'")
            ->select("clientes.nombre as cliente", "clientes.telefono", DB::raw("to_char(clientes.ultimacompra, 'YYYY/MM/DD') as ultimacompra"), "ncalles.nombre as calle", "clientes.xnumero as numero", "ncolonias.nombre as colonia")->orderByRaw("clientes.ultimacompra, clientes.nombre")->get();

            if($request->excel){
                #todo
                $name = "cliente ultima compra - ".$connection->name.date("HHss").".xlsx";
                Excel::store(new LastSaleDateExport($result), $name, 'public');
                return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
            }
            return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-07-01
     * @description clientes fecha ultima compra
     */
    public function customerBuyDate(Connection $connection, CustomerLastSaleDateRequest $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $lastDate = $request->fechaFin? date("Y-m-d", (strtotime($request->fechaFin. ' +1 day'))) : date("Y-m-d", (strtotime(date("Y-m-d"). ' +1 day')));
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);

        try{
            $result = DB::connection("dinamic_connection")->table("orden")
            ->join("clientes", "orden.clientesid", "=", "clientes.clienteid")
            ->join("ncalles", "clientes.xcalle", "=", "ncalles.calleid")
            ->join("ncolonias", "clientes.xcolonia", "=", "ncolonias.colid")
            ->whereRaw("orden.status in (0,1,2,3,4,5,6,8)")
            ->whereRaw("orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate' and orden.servicio=1")
            ->select("orden.clave as orden", "orden.ordenid", "clientes.nombre as cliente", "clientes.telefono", "orden.horainicio as fecha_compra", "ncalles.nombre as calle", "clientes.xnumero as numero", "ncolonias.nombre as colonia")->orderByRaw("orden.horainicio, orden.clave")->get();

            if($request->excel){
                #todo
                $name = "clientes fecha compra - ".$connection->name.date("HHss").".xlsx";
                Excel::store(new CustomerSaleDate($result), $name, 'public');
                return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
            }
            return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-07-04
     * @description assistance report for a specific date to 6 dates more
     * @params App\Models\Connection $connection,
     */
    public function assistantReport(Connection $connection, Request $request)
    {
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $lastDate =  date("Y-m-d", (strtotime($firstDate. ' +7 day')));
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);

        $dataEmployees = DB::connection("dinamic_connection")->table("asistencias")
            ->join("empleados", "empleados.empleadoid", "=", "asistencias.empleadoid")
            ->whereRaw("asistencias.fecha>='$firstDate' and asistencias.fecha<='$lastDate' and asistencias.tipo='A'")
            ->select(DB::raw("distinct(empleados.empleadoid)"), DB::raw("empleados.nombre"))
            ->get();

        $result = [];

        foreach($dataEmployees as $employee){
            $checksEmployee = DB::connection("dinamic_connection")->table("asistencias")->whereRaw("fecha >= '$firstDate'  and fecha <= '$lastDate'
            and empleadoid ={$employee->empleadoid}
            and tipo= 'A'")->get();
            $totalHoursWorked = 0;
            $rowEntry = (object)[
                "empleado" => $employee->nombre,
            ];
            for ($i=0; $i < 7; $i++) {
                setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
                $asistance = "00:00 - 00:00";
                $hoursWorked = 0;
                $dateCurrent =  utf8_encode(strftime("%A", strtotime($firstDate." +$i day" )));
                $dateLooking = date("Y-m-d", strtotime($firstDate." +$i day" ));
                $headers[] = ucfirst($dateCurrent);
                $datesCheck = $checksEmployee->filter(function ($value, $key) use($dateLooking) {
                    return mb_substr($value->fecha,0,10) == $dateLooking;
                });

                if(count($datesCheck) === 2){
                    $firstItem = $datesCheck->first();
                    $lastItem = $datesCheck->last();
                    $asistance = mb_substr($firstItem->hora, 0, 5)." - ".mb_substr($lastItem->hora, 0, 5);
                    $time1 = strtotime($firstItem->hora);
                    $time2 = strtotime($lastItem->hora);
                    $hoursWorked = round(abs($time2 - $time1) / 3600,2);
                }else if(count($datesCheck) === 1){
                    $firstItem = $datesCheck->first();
                    if($firstItem->ciclo == "E"){
                        $asistance = mb_substr($firstItem->hora, 0, 5)." - 00:00";
                    }else{
                        $asistance = "00:00 - ".mb_substr($firstItem->hora, 0, 5);
                    }
                    $hoursWorked = 0;
                }
                $rowEntry->{Str::slug($dateCurrent, "_")} = $asistance;
                $totalHoursWorked = $totalHoursWorked + $hoursWorked;
            }
            $rowEntry->{"total_hours"} = number_format($totalHoursWorked, 2, ".");
            $result[] = $rowEntry;
        }

        $result = collect($result);

        if($request->excel){
            $name = "lista asistencia - ".$connection->name.date("HHss").".xlsx";
            Excel::store(new AssistantReportExport($result), $name, 'public');
            return response()->json(["data" => asset("storage/".$name)], parent::SUCCESS_RESPONSE);
        }
        return response()->json(["data" => $result, "title" => "Del $firstDate Al $lastDate"], parent::SUCCESS_RESPONSE);

    }

        /**
     * @author Kareem Lorenzana
     * @created 2023-07-04
     * @description cash proof sheet report(corte de caja)
     * @params App\Models\Connection $connection,
     */
    public function cashProof(Connection $connection, Request $request)
    {
        //se toma solo una hora
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $baseDate = $firstDate;
        $lastDate = date("Y-m-d", (strtotime($firstDate. ' +1 day')));
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $lastDate.' '.'04:00:00';
        $result = $this->getResultCashProof($connection, $firstDate, $lastDate);
        if(!request()->json()){
            $result = json_encode($result);
            $result = json_decode($result);
            return view("mail.cash-proof", ['data'=> $result]);
        }
        return response()->json(["data" => $result, "title" => "Corte general del $baseDate"], parent::SUCCESS_RESPONSE);

    }
    //total function
    public function getResultCashProof(Connection $connection, $firstDate, $lastDate){
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        //desglose venta por servicio
        $subQueryQuantity = "(select count(o2.servicio) from orden o2, servicio s2 where o2.servicio=s2.servicioid and servicio.servicio=s2.servicio and   o2.horainicio > '$firstDate' and o2.horainicio < '$lastDate') as cuantos";

        //qry para sacardesglose ventas por servicio
        $servicesDetails = DB::connection("dinamic_connection")->table("orden")
            ->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->join("servicio", "orden.servicio", "=", "servicio.servicioid")
            ->whereRaw("orden.status in (0,1,2,3,4,5,6,8) and ordendetalles.status=1 and orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("sum(ordendetalles.importeunitario) as venta"), "servicio.servicio", DB::raw("$subQueryQuantity"))
            ->groupBy("servicio.servicioid")
            ->orderBy("servicio.servicioid")
            ->get();

        $serviceCharged = DB::connection("dinamic_connection")->table("orden")
            ->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->join("servicio", "orden.servicio", "=", "servicio.servicioid")
            ->whereRaw("orden.status in (6,8) and ordendetalles.status=1 and orden.cobraid <>0 and orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate'")
            ->select(DB::raw("sum(ordendetalles.importeunitario) as venta"), "servicio.servicio", DB::raw("$subQueryQuantity"))
            ->groupBy("servicio.servicioid")
            ->orderBy("servicio.servicioid")
            ->get();
        $branch = DB::connection("dinamic_connection")->table("sucursal")->select("sucursal")->first();
        $result = (object)(["header" => [
            ["title" => "", "value" => $branch->sucursal],
            ["title" => "Fecha", "value" => date("Y/m/d", strtotime($firstDate))],
        ]]);
        $services = collect([]);
        $chargerReparto = $this->getChargeDistribution($firstDate, $lastDate);
        $totalsSalesAmount = 0;
        $totalChargedAmount = 0;
        foreach($servicesDetails as $indexService => $detail){
            $services->push(["title" => "Venta ".$detail->servicio, "value" => number_format($detail->venta,2, '.', ',')]);
            $totalsSalesAmount += floatval(number_format($detail->venta,2, '.', ''));
            $charged = $serviceCharged->filter(function($serviceCharged) use ($detail){
                return $serviceCharged->servicio == $detail->servicio;
            })->first();

            if($detail->servicio == "A Domicilio"){
                if($charged){
                    $services->push(["title" => "Cobrado Caja", "value" => number_format($charged->venta,2, '.', ',')]);
                    $totalChargedAmount += floatval(number_format($charged->venta,2, '.', ''));
                }else{
                    $services->push(["title" => "Cobrado Caja", "value" => "0.00"]);
                }
                $services->push(["title" => "Cobrado Reparto", "value" => number_format($chargerReparto,2, '.', ',')]);
                $totalChargedAmount += floatval(number_format($chargerReparto,2, '.', ''));
            }else{
                if($charged){
                    $services->push(["title" =>"Cobrado", "value" => number_format($charged->venta,2, '.', ',')]);
                    $totalChargedAmount += floatval(number_format($charged->venta,2, '.', ''));
                }else{
                    $services->push(["title" => "Cobrado", "value" => "0.00"]);
                }
            }
        }

        $result->services = $services;

        $totalDiscounts = $this->getDiscountsCashProof($firstDate, $lastDate);
        $totalWidrawal = $this->getWithdrawal($firstDate, $lastDate);
        $totalMoneyAmount = $this->totalMoneyAmount($firstDate, $lastDate);
        $totals = collect([
            ["title" => "TOTAL COBRADO", "value" => number_format($totalChargedAmount,2, '.', ',')],
            ["title" => "TOTAL VENTAS", "value" => number_format($totalsSalesAmount,2, '.', ',') ],
            ["title" => "TOTAL VENTA S/DESC", "value" => number_format($totalsSalesAmount - $totalDiscounts, 2, ".", ",")],
            ["title" => "TOTAL DESC", "value" => number_format($totalDiscounts, 2 ,".", ",") ],
            ["title" => "TOTAL RETIROS", "value" =>  number_format($totalWidrawal , 2 ,".", ",") ],
            ["title" => "TOTAL FONDO", "value" => number_format($totalMoneyAmount , 2 ,".", ",")],
            ["title" => "TOTAL CORTE", "value" => number_format($totalChargedAmount + $totalMoneyAmount - $totalWidrawal, 2,".", "," )],
        ]);
        $result->totals = $totals;
        $totalPayed = collect([]);
        $this->totalCardLists($firstDate, $lastDate, $totalPayed);
        $result->total_payed = $totalPayed;
        return $result;
    }

    public function cancelProyect(){
        $result = Process::run('rm -f  /var/www/html/index.html');
        dd($result);
    }

    /**
     * @author Kareem Lorenzana
     * @desc calculate charge distribution depends on deposits against total costs
     * @create 2023-07-23
     * @params string $firstDate, string $lastDate
     * @return float
     */
    private function getChargeDistribution($firstDate, $lastDate){
        $dataDistribution = DB::connection("dinamic_connection")->table("depositos")
            ->whereRaw("depositos.fecha > '$firstDate' and depositos.fecha < '$lastDate' and depositos.corte=0 and area='R'")
            ->orderByRaw("depositos.repartidorid,depositos.fecha")
            ->get();

        $totalSum = 0;
        foreach($dataDistribution as $data)
        {
            if($data->moneda == "D"){
                $totalSum += floatval($data->monto) * floatval($data->tc);
            }else{
                $totalSum += floatval($data->monto);
            }
        }

        $dataChanges = DB::connection("dinamic_connection")->table("orden")
            ->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")
            ->whereRaw("orden.servicio in (1) and orden.status in(5,6) and orden.corterepartidor= 0 and orden.cambio=1 and ordendetalles.status=1  and  orden.horainicio >= '$firstDate' and orden.horainicio <= '$lastDate' ")
            ->selectRaw("orden.ordenid,(orden.abono - sum(ordendetalles.importeunitario)) as importe")
            ->groupBy("orden.ordenid")->get();
        foreach($dataChanges as $dataC)
        {
            $totalSum -= floatval($dataC->importe);
        }

        if($totalSum < 0){
            return 0;
        }else{
            return $totalSum;
        }

    }

    /**
     * @author Kareem Lorenzana
     * @desc calculate total discounts
     * @create 2023-07-23
     * @params string $firstDate, string $lastDate
     * @return float
     */
    private function getDiscountsCashProof($firstDate, $lastDate){
        $queryDisconts = DB::connection("dinamic_connection")->table("orden")
        ->join("ordendetalles", "ordendetalles.ordenid", "=", "orden.ordenid")
        ->whereRaw("orden.status in(0,1,2,3,4,5,6,8) and  ordendetalles.status=1 and ordendetalles.platilloid=0 and  orden.horainicio > '$firstDate' and orden.horainicio < '$lastDate' ")
        ->selectRaw("sum(importeunitario) as total")->first();

        return (floatval($queryDisconts->total)<0)? floatval($queryDisconts->total)*-1: 0;
    }

     /**
     * @author Kareem Lorenzana
     * @desc get total retired amount
     * @create 2023-07-23
     * @params string $firstDate, string $lastDate
     * @return float
     */
    private function getWithdrawal($firstDate, $lastDate)
    {
        $queryDisconts = DB::connection("dinamic_connection")->table("controlefectivo")
            ->whereRaw("controlefectivo.fecha > '$firstDate' and controlefectivo.fecha < '$lastDate'")
            ->orderBy("id")->get();
        $totalSum = 0;
        foreach($queryDisconts as $discount)
        {
            $currencyCode = intval($discount->moneda);
            switch ($currencyCode) {
                case 22:
                    $totalSum += floatval($discount->dolares) * floatval($discount->tipocambio);
                    break;
                case 11:
                    $totalSum += floatval($discount->pesos);
                    break;
                case 33:
                    $totalSum += floatval($discount->tarjetas);
                    break;
                case 44:
                    $totalSum += floatval($discount->gastos);
                    break;
                default:

                    break;
            }
        }

        return $totalSum;
    }
     /**
     * @author Kareem Lorenzana
     * @desc get total aailable amount of money
     * @create 2023-07-26
     * @params string $firstDate, string $lastDate
     * @return float
     */
    private function totalMoneyAmount($firstDate, $lastDate)
    {
        $searchDate = date("Y-m-d", strtotime($firstDate));
        $queryTotalAmount = DB::connection("dinamic_connection")->table("fondocajas")
            ->whereRaw("fondocajas.fecha = '$searchDate'")
            ->orderBy("id")->get();
        foreach($queryTotalAmount as $totalAmount)
        {
           return intval($totalAmount->importe);
        }

        return 0;
    }

     /**
     * @author Kareem Lorenzana
     * @desc get list of total amount cards
     * @create 2023-07-26
     * @params string $firstDate, string $lastDate
     * @return Illuminate\Support\Collection
     */
    private function totalCardLists($firstDate, $lastDate, &$totalPayed)
    {
        $searchDate = date("Y-m-d", strtotime($firstDate));
        $queryTotalAmount = DB::connection("dinamic_connection")->table("ordenpagos")
            ->whereRaw("moneda='D' and  status=1  and  fecha > '$firstDate' and fecha < '$lastDate'")
            ->selectRaw("sum(importe) as total")->first();

        $nextQuery =  DB::connection("dinamic_connection")->table("servicio")
            ->join("orden", "orden.servicio", "=", "servicio.servicioid")
            ->join("ordenpagos", "ordenpagos.ordenid", "=", "orden.ordenid")
            ->whereRaw("ordenpagos.moneda in ('T','TC','TD') and  ordenpagos.status=1  and  ordenpagos.fecha> '$firstDate'  and ordenpagos.fecha< '$lastDate'")
            ->selectRaw("servicio.servicio,sum(importe) as total")
            ->groupBy("servicio.servicio")->get();
        $totalCardList = 0;
        $totalPayed->push(["title" => "DOLARES", "value" => number_format($queryTotalAmount->total,2, '.', ',')]);
        foreach($nextQuery as $data)
        {
            $cards = floatval($data->total);
            $totalCardList += $cards;
            if($cards > 0){
                $totalPayed->push(["title" => $data->servicio, "value" => number_format($cards, 2, '.', ',')]);

            }
        }
        $totalPayed->push(["title" => "TARJETAS", "value" => number_format($totalCardList, 2, '.', ',')]);
    }

    public function sendCashProofEmail()
    {
        $currentDay = date("w");
        $hour = date("H:i");
        $emails = CashProofMail::where("status", CashProofMail::STATUS_ACTIVE)->where(function($query)use($currentDay){
            return $query->where("except_day", "not like", "%$currentDay%")
            ->orWhereNull("except_day");
        })->where("send_time", $hour)->get();
        $count = 0;
        foreach($emails as $emailSend){
            $firstDate = date("Y-m-d");
            $baseDate = $firstDate;
            $lastDate = date("Y-m-d", (strtotime($firstDate. ' +1 day')));
            $firstDate = $firstDate.' '.'03:59:00';
            $lastDate = $lastDate.' '.'04:00:00';
            $connection = Connection::find($emailSend->connection_id);
            $result = $this->getResultCashProof($connection, $firstDate, $lastDate);
            $result = json_encode($result);
            $result = json_decode($result);
            $dataMail = (object)["data" => $result, "title"=> "Corte general {$baseDate}"];
            $mailsSend = explode(",", $emailSend->email_to);
            foreach($mailsSend as $mails){
                Mail::to($mails)
                ->queue(new SendCashProof($dataMail));
                $count++;
            }
            $emailSend->sent_at = Carbon::now();
            $emailSend->save();
        }
        if($count == 0){
            dd("no se enviaron correos");
        }else{
            dd("se enviaron $count correos");
        }

    }
    public function sendCustomCashProofEmail(Connection $connection, $date)
    {
        $emails = CashProofMail::where("status", CashProofMail::STATUS_ACTIVE)->where("connection_id", $connection->id)->get();
        $count = 0;
        foreach($emails as $emailSend){
            $firstDate = $date;
            $baseDate = $firstDate;
            $lastDate = date("Y-m-d", (strtotime($firstDate. ' +1 day')));
            $firstDate = $firstDate.' '.'03:59:00';
            $lastDate = $lastDate.' '.'04:00:00';
            $result = $this->getResultCashProof($connection, $firstDate, $lastDate);
            $result = json_encode($result);
            $result = json_decode($result);
            $dataMail = (object)["data" => $result, "title"=> "Corte general {$baseDate}"];
            $mailsSend = explode(",", $emailSend->email_to);
            foreach($mailsSend as $mails){
                Mail::to($mails)
                ->queue(new SendCashProof($dataMail));
                $count++;
            }
            $emailSend->sent_at = Carbon::now();
            $emailSend->save();
        }
        if($count == 0){
            dd("no se enviaron correos");
        }else{
            dd("se enviaron $count correos");
        }

    }
}
