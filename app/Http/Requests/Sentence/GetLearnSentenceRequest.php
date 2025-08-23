<?php

namespace App\Http\Requests\Sentence;

use App\Http\Requests\ApiFormRequest;

class GetLearnSentenceRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sentence_id' => 'nullable|integer|exists:en_sentences,id',
            'action' => 'nullable|string|in:up,down',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'sentence_id' => $this->input('sentence_id', null),
            'action' => $this->input('action', null),
        ]);
    }
}
