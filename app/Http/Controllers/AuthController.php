<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $user;

    /**
     * @author Kareem Lorenzana
     * @created 2023-10-17
     * @params App\Models\User
     * @return void
     * Initialize vars for current controller
     */
    public function __construct(Employee $user){
        $this->user = $user;
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-10-17
     * @params App\Models\User, Illuminate\Http\Request
     * @return Illuminate\Http\JsonResponse
     *login application for user request
     */
    public function login (Request $request)
    {
        $user = $this->user->where('clave', $request->username)->where("status", Employee::STATUS_ALTA)->first();
        if ($user) {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            $response = ['token' => $token];
            return response()->json($response, parent::SUCCESS_RESPONSE);
        } else {
            $response = ["message" => "Usuario incorrecto"];
            return response()->json($response, parent::UNPROCESSABLE_ENTITY_RESPONSE);
        }
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-10-17
     * @params App\Models\User
     * @return Illuminate\Http\JsonResponse
     * logout application for current user
     */
    public function logout(){
        if(auth()->check()){
            $user = Auth::user()->token();
            $user->revoke();
        }

        return response()->json([
            'message' => 'Sesión terminada correctamente'
        ], parent::SUCCESS_RESPONSE);
    }


    /**
     * @author Kareem Lorenzana
     * @created 2023-10-17
     * @params
     * @return Illuminate\Http\JsonResponse
     * get current profile information for autenticate user
     */
    public function profile()
    {
        $user = $this->user->with(USER::RELATIONS)->find(auth()->user()->id);
        $data = (object)["data" => $user];
        return response()->json($data, parent::SUCCESS_RESPONSE);
    }

}
