<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

abstract class ApiFormRequest extends FormRequest
{
    public function response(array $errors)
    {
        if ($this->expectsJson()) {
            return new JsonResponse($errors, 422);
        }

        return $this->redirector
            ->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))->withErrors($errors, $this->errorBag);
    }

    public function failedValidation(Validator $validator)
    {
        // Отправляем ошибку в ответ
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }

    protected function userLearnLanguage()
    {
        return auth()->user()->languageUser->learnLanguage->language;
    }

    /**
     * Проверка на присутствие записи в таком поле
     */
    protected function CheckExistsInDB($string, $field)
    {
        return "exists:{$this->userLearnLanguage()}{$string},{$field}";
    }

    /**
     * Проверка на уникальность записи с возможным параметром - исключая запись с этим id
     */
    protected function CheckUniqueInDB($string, $field, $excludeId = null): string
    {
        $rule = "unique:{$this->userLearnLanguage()}{$string},{$field}";
        // если передали id исключения записи
        if ($excludeId) {
            $rule .= ",{$excludeId}";
        }

        return $rule;
    }

    abstract public function authorize();

    abstract public function rules();
}
