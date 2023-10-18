<?php

namespace App\Http\Requests\Connection;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ConnectionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @author Kareem Lorenzana
     * @created 2022-06-01
     * @return bool
     */
    public function authorize():bool
    {
        return auth()->check() && auth()->user()->user_type == User::USER_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     * @author Kareem Lorenzana
     * @created 2022-06-01
     * @return array
     */
    public function rules():array
    {
        return [
            'name' => 'required|string|min:3|max:50|unique:connections,name',
            'host' =>  'required|string|min:3|max:50',
            'username' =>  'required|string|min:3|max:50',
            'password' =>  'required|string|min:3|max:50',
            'port' =>  'required|string|min:3|max:50',
            'database' =>  'required|string|min:3|max:50',
            'description' =>  'required|string|min:3|max:300',
        ];
    }

    /**
     * Get messages for request
     * @author Kareem Lorenzana
     * @created 2022-06-01
     * @return array
     */
    public function messages():array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El tipo de dato para el nombre es texto',
            'name.min' => 'El mínimo permitido para el nombre es de 3 caracteres',
            'name.max' => 'El máximo permitido para el nombre es de 50 caracteres',
            'name.unique' => 'El nombre que intenta registrar ya se encuentra en uso',
            'host.required' => 'El host es obligatorio',
            'host.string' => 'El tipo de dato para host es texto',
            'host.min' => 'El mínimo permitido para el host es de 3 caracteres',
            'host.max' => 'El máximo permitido para el host es de 50 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.string' => 'El tipo de dato para nombre de usuario es texto',
            'username.min' => 'El mínimo permitido para el nombre de usuario es de 3 caracteres',
            'username.max' => 'El máximo permitido para el nombre de usuario es de 50 caracteres',
            'password.required' => 'La contraseña es un dato obligatorio',
            'password.string' => 'El tipo de dato para contraseña es texto',
            'password.min' => 'El mínimo permitido para la contraseña es de 3 caracteres',
            'password.max' => 'El máximo permitido para la contraseña es de 50 caracteres',
            'port.required' => 'El puerto es obligatorio',
            'port.string' => 'El tipo de dato para puerto es texto',
            'port.min' => 'El mínimo permitido para el puerto es de 3 caracteres',
            'port.max' => 'El máximo permitido para el puerto es de 50 caracteres',
            'database.required' => 'La base de datos es obligatoria',
            'database.string' => 'El tipo de dato para base de datos es texto',
            'database.min' => 'El mínimo permitido para la base de datos es de 3 caracteres',
            'database.max' => 'El máximo permitido para la base de datos es de 50 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'description.string' => 'El tipo de dato para descripción es texto',
            'description.min' => 'El mínimo permitido para la descripción es de 3 caracteres',
            'description.max' => 'El máximo permitido para la descripción es de 300 caracteres',
        ];
    }
}
