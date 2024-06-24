<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UpdateSentenceRequest extends ApiFormRequest
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
        $sentenceId = $this->route('sentence_id') ?? $this->input('sentence_id');

        return [
            'sentence_id' => [
                'required',
                'integer',
                $this->CheckExistsInDB('_sentences', 'id'),
            ],
            'sentence' => [
                'required',
                'string',
                'min:3',
                $this->CheckUniqueInDB('_sentences', 'sentence', $sentenceId),
            ],
            'translation' => 'nullable|string|min:3',
        ];
    }

}
