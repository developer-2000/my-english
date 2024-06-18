<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Разрешить использование запроса всем пользователям
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ];
    }
}
