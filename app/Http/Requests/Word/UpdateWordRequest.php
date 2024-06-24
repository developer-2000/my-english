<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UpdateWordRequest extends ApiFormRequest
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
     */
    public function rules() {
        $wordId = $this->route('word_id') ?? $this->input('word_id');

        return [
            'word_id' => [
                'required',
                'integer',
                $this->CheckExistsInDB('_words', 'id'),
            ],
            'word' => [
                'required',
                'string',
                'min:1',
                $this->CheckUniqueInDB('_words', 'word', $wordId),
            ],
            'translation' => 'required|string|min:1',
            'url_image' => 'nullable|string',
            'description' => 'nullable|string',
            'type_id' => 'nullable|integer',
            'time_forms' => 'nullable',
            'arr_new_sentences' => 'nullable|array',
        ];
    }

}
