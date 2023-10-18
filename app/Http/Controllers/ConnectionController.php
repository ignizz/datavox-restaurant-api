<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\Connection\ConnectionStoreRequest;
use App\Http\Requests\Connection\ConnectionFilterRequest;
use App\Http\Requests\Connection\ConnectionUpdateRequest;

class ConnectionController extends Controller
{
    private $connection;
    /**
     * @author Kareem Lorenzana
     * @created 2023-06-01
     * @params App\Models\Connection
     * @return void
     * Initialize vars for current controller
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-06-01
     * @params App\Http\Requests\Connection\ConnectionFilterRequest
     * @return Illuminate\Http\JsonResponse
     *
     */
    public function list(ConnectionFilterRequest $request)
    {
        $rowsPerPage = $request->rowsPerPage? $request->rowsPerPage: 25;
        $connections = $this->connection->filterData($request);
        $connections = $connections->paginate($rowsPerPage);
        $response = (object)["data" => $connections];
        return response()->json($response, parent::SUCCESS_RESPONSE);
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-06-01
     * @params App\Http\Requests\Connection\ConnectionStoreRequest
     * @return Illuminate\Http\JsonResponse
     * return
     */
    public function store(ConnectionStoreRequest $request)
    {
        $connectionData = [
            "name" => $request->name,
            "host" => $request->host,
            "username" => $request->username,
            "password" => $request->password,
            "port" => $request->port,
            "database" => $request->database,
            "description" => $request->description
        ];
        $connection = null;
        if($request->test === "ok"){
            $connection = $connectionData;
        }else{
            $connection = $this->connection->create($connectionData);
        }
        return response()->json(["data" => $connection], parent::SUCCESS_RESPONSE);
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-06-01
     * @params App\Models\Connection
     * @return Illuminate\Http\JsonResponse
     * return user information for update
     */
    public function edit(Connection $connection){
        return response()->json(["data" => $connection], parent::SUCCESS_RESPONSE);
    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-06-01
     * @params App\Models\Connection, App\Http\Requests\Connection\ConnectionUpdateRequest
     * @return Illuminate\Http\JsonResponse
     * return connection information for updated connection
     */
    public function update(ConnectionUpdateRequest $request, Connection $connection){
        $connectionData = [
            "name" => $request->name,
            "host" => $request->host,
            "username" => $request->username,
            "password" => $request->password,
            "port" => $request->port,
            "database" => $request->database,
            "description" => $request->description,
            "status" => $request->status
        ];

        if($request->test === "ok"){
            $connection = $connectionData;
        }else{
            try{
                $connection = $connection->update($connectionData);
            }catch(\Exception $e){
                return response()->json(["data" => $e->getMessage()], parent::SUCCESS_RESPONSE);
            }
        }
        return response()->json(["data" => $connection], parent::SUCCESS_RESPONSE);
    }

    public function setConnection($name){
        $dinamicConnection = Connection::where('name', $name)->first();
        $message = "No se pudo conectar correctamente";
        // Verifica si se encontró la conexión dinámica
        if ($dinamicConnection) {
            $message = "Conexión establecida de forma correcta";
            config(['database.connections.dinamic_connection.host' => $dinamicConnection->host]);
            config(['database.connections.dinamic_connection.port' => $dinamicConnection->port]);
            config(['database.connections.dinamic_connection.database' => $dinamicConnection->database]);
            config(['database.connections.dinamic_connection.username' => $dinamicConnection->username]);
            config(['database.connections.dinamic_connection.password' => $dinamicConnection->password]);
        }

        return response()->json(["data" => $message], parent::SUCCESS_RESPONSE);

    }

    /**
     * @author Kareem Lorenzana
     * @created 2023/06/05
     * @params
     * @return Illuminate\Http\JsonResponse
     * get all active connections from database for load select information
     */
    public function listAll(){
        try{
            $connections = $this->connection->active()->get();
        }catch(\Exception $e){
            return response()->json($e->getMessage(), parent::ERROR_RESPONSE);
        }
        return response()->json(["data" => $connections], parent::SUCCESS_RESPONSE);
    }
}
