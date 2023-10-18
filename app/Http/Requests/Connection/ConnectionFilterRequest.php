<?php

namespace App\Http\Requests\Connection;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ConnectionFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->user_type == User::USER_ADMIN;
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
            'descending' => 'nullable|in:true,false',
            'rowsPerPage' => 'nullable|numeric',
            'filter' => 'nullable|string'
        ];
    }
}
