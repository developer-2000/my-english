<?php
namespace App\Http\Requests\AIStudio;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class GenerateSentencesRequest extends ApiFormRequest {

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
            'arr_words' => 'required|array',
            'arr_words.*' => 'string', // каждое слово в массиве должно быть строкой
        ];
    }
}
