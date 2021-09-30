<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class UpdateSentenceRequest extends ApiFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    // отловить параметр get и передача в request
    public function all($keys = null) {
        $data = parent::all($keys);
        $data['id'] = $this->route('sentence');
        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * правила проверки
     */
    public function rules() {
        return [
            'id' => 'required|integer|exists:sentences,id',
            'sentence' => 'required|string|min:3|unique:sentences,sentence,'.$this->id,
            'translation' => 'required|string|min:3',
        ];
    }

}
