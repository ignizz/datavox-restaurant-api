<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    private $table;
    /**
     * @author Kareem Lorenzana
     * @created 2023-10-25
     * @params App\Models\Table
     * @return void
     * Initialize vars for current controller
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-10-25
     * @description get all tables from list
     * @params
     * @return json
     */
    public function list(){
        $tables = $this->table->whereNotNull("nombre")->get();
        $response = (object)["data" => $tables];
        return response()->json($response, parent::SUCCESS_RESPONSE);
    }
}
