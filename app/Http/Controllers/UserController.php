<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserFilterRequest;
use App\Http\Requests\User\UserUpdateRequest;

class UserController extends Controller
{
    private $user;
    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Models\User
     * @return void
     * Initialize vars for current controller
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Http\Requests\UserFilterRequest
     * @return Illuminate\Http\JsonResponse
     *
     */
    public function list(UserFilterRequest $request)
    {
        $rowsPerPage = $request->rowsPerPage? $request->rowsPerPage: 25;
        $users = $this->user->filterData($request);
        $users = $users->paginate($rowsPerPage);
        $response = (object)["data" => $users];
        return response()->json($response, parent::SUCCESS_RESPONSE);
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Http\Requests\User\UserStoreRequest
     * @return Illuminate\Http\JsonResponse
     * return
     */
    public function store(UserStoreRequest $request)
    {
        $userData = [
            "id" => 0,
            "first_name" => $request->{"first-name"},
            "last_name" => $request->{"last-name"},
            "username" => $request->{"username"},
            "password" => Hash::make($request->password),
            "email" => $request->email,
            "status" => User::STATUS_ACTIVE,
            "user_type" => $request->{"user-type"}
        ];
        if($request->test === "ok"){
            $user = $userData;
        }else{
            $user = $this->user->create($userData);
            if($request->connections && is_array($request->connections)){
                $user->connections()->sync($request->connections);
            }
        }
        return response()->json(["data" => $user], parent::SUCCESS_RESPONSE);
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Models\User
     * @return Illuminate\Http\JsonResponse
     * return user information for update
     */
    public function edit(User $user){
        $user = $this->user->with(USER::RELATIONS)->find($user->id);
        return response()->json(["data" => $user], parent::SUCCESS_RESPONSE);
    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-05-19
     * @params App\Models\User, App\Http\Requests\User\UserUpdateRequest
     * @return Illuminate\Http\JsonResponse
     * return user information for update
     */
    public function update(UserUpdateRequest $request, User $user){
        $userData = [
            "first_name" => $request->{"first-name"},
            "last_name" => $request->{"last-name"},
            "email" => $request->email,
            "status" => User::STATUS_ACTIVE,
            "user_type" => $request->{"user-type"}
        ];

        $user->first_name = $request->{"first-name"};
        $user->last_name = $request->{"last-name"};
        $user->email = $request->email;
        //validate tha the current user cannot modify his own user
        if(auth()->user()->user_type == User::USER_ADMIN && auth()->user()->id != $user->id && $user->id != User::PRINCIPAL_USER)
        {
            $user->user_type = $request->{"user-type"};
            $user->status = $request->status;
            if($request->password && strlen($request->password) > 0){
                $user->password = Hash::make($request->password);
            }
        }
        if($request->test === "ok"){
            $user = $userData;
        }else{
            $user->save();
            if($request->connections && is_array($request->connections)){
                $user->connections()->sync($request->connections);
            }else{
                $user->connections()->delete();
            }
        }
        return response()->json(["data" => $user], parent::SUCCESS_RESPONSE);
    }
}
