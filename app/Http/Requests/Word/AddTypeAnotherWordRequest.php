<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class AddTypeAnotherWordRequest extends ApiFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * правила проверки
     */
    public function rules() {
        return [
            'from_word_id' => 'required|integer|exists:words,id',
            'to_word_text' => 'required|string|min:2',
        ];
    }

}
