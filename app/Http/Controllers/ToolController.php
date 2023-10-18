<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Connection;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\SuppliesInventory;
use App\Models\MovementsInventory;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Order\FilterOrderRequest;

class ToolController extends Controller
{
    public function showTypeOfExchange(Connection $connection)
    {
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $result = DB::connection("dinamic_connection")->table("sucursal")->select("sucursal", "tipodecambio")->first();
            return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }
    public function updateTypeOfExchange(Connection $connection, Request $request)
    {
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        try{
            $result = DB::connection("dinamic_connection")->table("sucursal")->first();
            if($result){
                DB::connection("dinamic_connection")->table("sucursal")->update(["tipodecambio" => $request->tipodecambio]);
            }
            return response()->json(["data" => $result], parent::SUCCESS_RESPONSE);
        }catch(\Exception $e){
            return response()->json(["data" => "Hubo un problema de conexión"], parent::ERROR_RESPONSE);
        }
    }

    /**
     * api for filter order
     * @author Kareem Lorenzana
     * @create 2023-08-16
     * @params App\Models\Connection $connection, App\Http\Requests\Order\FilterOrderRequest
     */
    public function filterOrder(Connection $connection, FilterOrderRequest $request)
    {
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);

        $orderData = Order::where("clave", $request->id)
            ->whereRaw("TO_DATE(to_char(horainicio,'YYYY-MM-DD'),'YYYY-MM-DD') = to_timestamp('$request->date', 'YYYY-MM-DD')")->with(["orderDetails" => fn ($query) => $query->orderBy('ordendetalleid', "asc"), "customerName", "service", "employeeName"])
            ->first();
        $result =  (object)["data" => $orderData, "message"=> "No se obtuvieron resultados"];
        if($orderData){
            $result =  (object)["data" => $orderData, "message"=> "datos cargados correctamente"];
        }

        return response()->json($result, parent::SUCCESS_RESPONSE);
    }
    /**
     * api for filter order
     * @author Kareem Lorenzana
     * @create 2023-08-16
     * @params App\Models\Connection $connection, App\Http\Requests\Order\FilterOrderRequest
     */
    public function getOrderById(Connection $connection, $order)
    {
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);

        $orderData = Order::where("ordenid", $order)
            ->with(["orderDetails" => fn ($query) => $query->orderBy('ordendetalleid', "asc"), "customerName", "service", "employeeName"])
            ->first();
        $result =  (object)["data" => $orderData, "message"=> "No se obtuvieron resultados"];
        if($orderData){
            $result =  (object)["data" => $orderData, "message"=> "datos cargados correctamente"];
        }

        return response()->json($result, parent::SUCCESS_RESPONSE);
    }

    /**
     * api for cancel dish in order
     * @author Kareem Lorenzana
     * @created 2023-08-20
     * @return json
     */
    public function cancelOrderDetail(Connection $connection,$orderDetail, Request $request)
    {
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        $orderDetail = OrderDetail::find($orderDetail);
        $response = [];
        if($orderDetail->status == OrderDetail::STATUS_CANCELED){
            $response= (object)["data" => $orderDetail, "message"=> "El platillo ya ha sido cancelado."];
            return response()->json($response, parent::SUCCESS_RESPONSE);
        }
        $orderDetail->status = OrderDetail::STATUS_CANCELED;
        $orderDetail->cancelaid = OrderDetail::CANCELA_ID;
        $orderDetail->cancelahora = Carbon::now();
        $orderDetail->observaciones = $request->comment;
        $orderDetail->save();
        $response = (object)["data" => $orderDetail, "message"=> "Se canceló de manera correcta"];
        return response()->json($response, parent::SUCCESS_RESPONSE);
    }
    /**
     * api for cancel order
     * @author Kareem Lorenzana
     * @created 2023-08-20
     * @return json
     */
    public function cancelOrder(Connection $connection, $order, Request $request)
    {
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);
        $response = [];
        $order = Order::find($order);
        if($order->status == Order::STATUS_CANCELED){
            $response= (object)["data" => $order, "message"=> "La orden ya ha sido cancelado."];
            return response()->json($response, parent::SUCCESS_RESPONSE);
        }
        $order->status = Order::STATUS_CANCELED;
        $order->cancelaid = Order::CANCELA_ID;
        $order->horacancela = Carbon::now();
        $order->observacion = $request->comment;
        $order->save();
        OrderPayment::where("ordenid",$order->ordenid)->update(["status" => OrderPayment::STATUS_CANCEL]);
        $response = (object)["data" => $order, "message"=> "Se canceló de manera correcta"];
        return response()->json($response, parent::SUCCESS_RESPONSE);
    }

    public function getInventorySuppliesIn(Connection $connection, Request $request){
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $lastDate = date("Y-m-d", (strtotime($firstDate. ' +1 day')));
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);


        $data = MovementsInventory::with(["supply"])->where("fechareg",">=", $firstDate)->where("fechareg","<=", $lastDate)->where("movimiento", MovementsInventory::MOVEMENT_IN)->orderBy("codigo", "asc")->get();
        $result =  (object)["data" => $data, "message"=> "datos cargados correctamente"];
        return response()->json($result, parent::SUCCESS_RESPONSE);

    }

    public function getInventorySuppliesOut(Connection $connection, Request $request){
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        $lastDate = date("Y-m-d", (strtotime($firstDate. ' +1 day')));
        $firstDate = $firstDate.' '.'03:59:00';
        $lastDate = $lastDate.' '.'04:00:00';
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);


        $data = MovementsInventory::with(["supply"])->where("fechareg",">=", $firstDate)->where("fechareg","<=", $lastDate)->where("movimiento", MovementsInventory::MOVEMENT_OUT)->orderBy("codigo", "asc")->get();
        $result =  (object)["data" => $data, "message"=> "datos cargados correctamente"];
        return response()->json($result, parent::SUCCESS_RESPONSE);

    }

    public function updateMovementSupplyQuantity(Connection $connection, $supply_id, Request $request){
        $firstDate = $request->fechaInicio? $request->fechaInicio : date("Y-m-d");
        config(['database.connections.dinamic_connection.host' => $connection->host]);
        config(['database.connections.dinamic_connection.port' => $connection->port]);
        config(['database.connections.dinamic_connection.database' => $connection->database]);
        config(['database.connections.dinamic_connection.username' => $connection->username]);
        config(['database.connections.dinamic_connection.password' => $connection->password]);


        $supply = MovementsInventory::find($supply_id);
        if($supply){
            $supply->cantidad = $request->quantity;
            $supply->save();
            $result =  (object)["data" => $supply, "message"=> "Cantidad modificada correctamente."];
        return response()->json($result, parent::SUCCESS_RESPONSE);
        }else{
            $result =  (object)["data" => (object)[], "message"=> "No se pudo modificar, dato no encontrado"];
            return response()->json($result, parent::SUCCESS_RESPONSE);
        }

    }
}
