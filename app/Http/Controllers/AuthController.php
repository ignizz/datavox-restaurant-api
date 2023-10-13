<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $user;

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Models\User
     * @return void
     * Initialize vars for current controller
     */
    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Models\User, Illuminate\Http\Request
     * @return Illuminate\Http\JsonResponse
     *login application for user request
     */
    public function login (Request $request)
    {
        $user = $this->user->where('username', $request->username)->where("status", User::STATUS_ACTIVE)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response()->json($response, parent::SUCCESS_RESPONSE);
            } else {
                $response = ["message" => "Usuario o contraseña incorrectas"];
                return response()->json($response, parent::UNPROCESSABLE_ENTITY_RESPONSE);
            }
        } else {
            dd($request->all());
            $response = ["message" =>'El usuario no existe'];
            return response()->json($response, parent::UNPROCESSABLE_ENTITY_RESPONSE);
        }
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
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
     * @created 2023-05-19
     * @params
     * @return Illuminate\Http\JsonResponse
     * get current profile information for autenticate user
     */
    public function profile()
    {
        $data = (object)["data" => auth()->user()];
        return response()->json($data, parent::SUCCESS_RESPONSE);
    }

}
