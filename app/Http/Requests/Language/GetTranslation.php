<?php

namespace App\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;

class GetTranslation extends FormRequest
{
    public function authorize()
    {
        return true; // Разрешаем использование запроса всем пользователям
    }

    public function rules()
    {
        return [
            'lang' => 'required|string|exists:languages,language',
        ];
    }

    public function messages()
    {
        return [];
    }
}
