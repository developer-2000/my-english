<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class UpdateWordRequest extends ApiFormRequest
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
        $data['id'] = $this->route('word');
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
            'id' => 'required|integer|exists:words,id',
            'word' => 'required|string|min:1|unique:words,word,'.$this->id,
            'translation' => 'required|string|min:1',
            'url_image' => 'nullable|string',
            'description' => 'nullable|string',
            'type_id' => 'nullable|integer|exists:word_types,id',
            'time_forms' => 'nullable',
            'arr_new_sentences' => 'nullable|array',
        ];
    }

}
