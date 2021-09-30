<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class CreateTypeRequest extends ApiFormRequest
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
            'type' => 'required|string|min:3|unique:word_types,type',
            'color' => 'required|string|min:3|unique:word_types,color',
            'description' => 'nullable|string|min:3',
        ];
    }

}
