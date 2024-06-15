<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class CreateWordRequest extends ApiFormRequest
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
            'word' => 'required|string|min:1|unique:words,word',
            'translation' => 'required|string|min:1',
            'url_image' => 'nullable|string',
            'description' => 'nullable|string',
            'type_id' => 'required|integer',
            'time_forms' => 'nullable',
            'arr_new_sentences' => 'nullable|array',
        ];
    }
}
