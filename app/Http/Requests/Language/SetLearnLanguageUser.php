<?php

namespace App\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;

class SetLearnLanguageUser extends FormRequest
{
    public function authorize()
    {
        return true; // Разрешить использование запроса всем пользователям
    }

    public function rules()
    {
        return [
            'language' => 'required|string|exists:languages,language',
        ];
    }

    public function messages()
    {
        return [
            'language.required' => 'Language code is required.',
            'language.string' => 'Language code must be a string.',
            'language.exists' => 'Invalid language code.',
        ];
    }
}
