<?php

namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteWordRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => [
                'required',
                'integer',
                $this->CheckExistsInDB('_words', 'id'),
            ],
        ];
    }
}
