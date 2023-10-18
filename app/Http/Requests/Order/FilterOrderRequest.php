<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class FilterOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d'
        ];
    }

    /**
     * Get messages for request
     * @author Kareem Lorenzana
     * @created 2023-08-16
     * @return array
     */
    public function messages(){
        return [
            'id.required' => 'El Número de orden es un dato obligatorio',
            'id.numeric' => 'El Número de orden debe ser un dato numérico',
            'date.required' => 'La fecha es obligatoria',
            'date.date_format' => 'El formato de fecha es inválido'
        ];
    }

}
