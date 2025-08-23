<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CreateSentenceRequest extends ApiFormRequest
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
            'sentence' => [
                'required',
                'string',
                'min:3',
            ],
            'translation' => 'required|string|min:3',
        ];
    }

}
