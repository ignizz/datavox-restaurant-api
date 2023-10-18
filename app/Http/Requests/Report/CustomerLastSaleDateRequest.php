<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLastSaleDateRequest extends FormRequest
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
            'fechaInicio' => 'nullable|date_format:Y-m-d',
            'fechaFin' => 'nullable|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'fechaInicio.required' => 'La primera fecha es obligatoria.',
            'fechaInicio.date_format' => 'La primera fecha debe tener el formato Y-m-d.',

            'fechaFin.required' => 'La segunda fecha es obligatoria.',
            'fechaFin.date_format' => 'La segunda fecha debe tener el formato Y-m-d.',
        ];
    }
}
