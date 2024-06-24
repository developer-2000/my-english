<?php
namespace App\Http\Requests\Sentence;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BindCheckboxSoundRequest extends ApiFormRequest
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
            'sentence_id' => [
                'required',
                'integer',
                $this->CheckExistsInDB('_sentences', 'id'),
            ],
            'status' => 'required|boolean',
        ];
    }

}
