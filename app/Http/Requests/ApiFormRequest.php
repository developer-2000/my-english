<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

abstract class ApiFormRequest extends FormRequest
{
//    protected function failedValidation(Validator $validator)
//    {
//        // все ошибки валидации
//        $errors = (new ValidationException($validator))->errors();
//
//        throw new HttpResponseException(response()->json([
//            'errors' => $errors
//        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
//    }

    public function response(array $errors)
    {
        if ($this->expectsJson()) {
            return new JsonResponse($errors, 422);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }

    abstract public function authorize();
    abstract public function rules();
}
