<?php
namespace App\Http\Requests\Word;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Http\JsonResponse;

class SelectGetPaginateRequest extends ApiFormRequest {

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
            'selection_type_id' => 'nullable|string',
            'sortField' => 'nullable|string',
            'sortType' => 'nullable|string',
            'search' => 'nullable|string',
            'page' => 'required|integer',
            'perPage' => 'required|integer',
        ];
    }

}
