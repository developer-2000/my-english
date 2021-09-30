<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class UpdateTypeRequest extends ApiFormRequest
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
        $data['id'] = $this->route('type');
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
            'id' => 'required|integer|exists:word_types,id',
            'type' => 'required|string|min:1|unique:word_types,type,'.$this->id,
            'color' => 'required|string|min:4|unique:word_types,type,'.$this->id,
            'description' => 'nullable|string|min:3',
        ];
    }

}
