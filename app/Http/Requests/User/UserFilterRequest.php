<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'page' => 'nullable|numeric',
            'descending' => 'nullable|boolean',
            'rowsPerPage' => 'nullable|numeric',
            'filter' => 'nullable|string'
        ];
    }
}
