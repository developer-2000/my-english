<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ...

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            // Ошибка валидации
            return response()->view('errors', [
                'code' => 422,
                'message' => $exception->getMessage(),
            ], 422);
        }

        if ($exception instanceof QueryException && $exception->getCode() === '23000') {
            // Ошибка дублирования уникального ключа
            return response()->view('errors', [
                'code' => 500,
                'message' => 'Duplicate entry error: '.$exception->getMessage(),
            ], 500);
        }

        return parent::render($request, $exception);
    }

    // ...
}
