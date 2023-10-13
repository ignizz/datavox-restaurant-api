<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @author Kareem Lorenzana
     * @created 2022-05-19
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @author Kareem Lorenzana
     * @created 2022-05-19
     * @return array
     */
    public function rules()
    {
        $userTypes = implode(",", User::USER_TYPES);
        return [
            'first-name' => 'required|string|min:3|max:50',
            'last-name' =>  'required|string|min:3|max:50',
            'password' =>  'required|string|min:3|max:50',
            'email' =>  'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'user-type' => "required|in:$userTypes"
        ];
    }

    /**
     * Get messages for request
     * @author Kareem Lorenzana
     * @created 2022-05-19
     * @return array
     */
    public function messages(){
        return [
            'first-name.required' => 'El nombre es obligatorio',
            'first-name.string' => 'El tipo de dato para nombre es texto',
            'first-name.min' => 'El minimo permitido para el nombre es de 3 caracteres',
            'first-name.max' => 'El máximo permitido para el nombre es de 50 caracteres',
            'last-name.required' => 'El apellido es obligatorio',
            'last-name.string' => 'El tipo de dato para apellido es texto',
            'last-name.min' => 'El minimo permitido para el apellido es de 3 caracteres',
            'last-name.max' => 'El máximo permitido para el apellido es de 50 caracteres',
            'password.required' => 'La contraseña es un dato obligatorio',
            'password.string' => 'El tipo de dato para contraseña es texto',
            'password.min' => 'El minimo permitido para la contraseña es de 3 caracteres',
            'password.max' => 'El máximo permitido para la contraseña es de 50 caracteres',
            'email.required' => 'El correo es requerido',
            'email.unique' => 'El correo que intenta registrar ya se encuentra en uso',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.unique' => 'El nombre de usuario que intenta utilizar ya se encuentra en uso',
            'user-type.required' => 'El tipo de usuario es obligatorio',
            'user-type.in' => 'El tipo de usuario que intenta agregar no es válido'
        ];
    }
}
