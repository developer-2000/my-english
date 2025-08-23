<?php

namespace App\Http\Requests\LearnWord;

use App\Http\Requests\ApiFormRequest;

class GetLearnWordRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *               правила проверки
     */
    public function rules()
    {
        return [
            'last_updated_at' => 'nullable|string',
            'last_word_id' => 'nullable|integer',
            'action_with_word' => 'nullable|string',
        ];
    }
}
