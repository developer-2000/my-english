<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CreateWordRequest extends ApiFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * правила проверки
     */
    public function rules() {
        return [
            'word' => [ 'required','string','min:1',
                $this->CheckUniqueInDB('_words', 'word'),
            ],
            'translation' => 'required|string|min:1',
            'url_image' => 'nullable|string',
            'description' => 'nullable|string',
            'type_id' => 'required|integer',
            'time_forms' => 'nullable',
            'arr_new_sentences' => 'nullable|array',
        ];
    }

    /**
     * Получаем пользовательские сообщения об ошибках для правил валидации.
     *
     * @return array
     */
    public function messages() {
        return [
            'word.unique' => "Такое слово уже существует в таблице слов.",
        ];
    }
}
