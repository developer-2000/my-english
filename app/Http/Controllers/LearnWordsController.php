<?php

namespace App\Http\Controllers;

use App\Http\Requests\LearnWord\GetLearnWordRequest;
use App\Http\Responses\ApiResponse;
use App\Repositories\LearnWordRepository;

class LearnWordsController extends Controller
{
    protected $learnWordRepository;

    public function __construct() {
        $this->learnWordRepository = new LearnWordRepository();
    }

    /**
     * Получить слово для изучения и обновить метку времени предыдущего слова, если необходимо.
     *
     * @param GetLearnWordRequest $request
     * @return ApiResponse
     */
    public function getLearnWord(GetLearnWordRequest $request): ApiResponse
    {
        // Получаем слово для изучения из репозитория
        $latestWord = $this->learnWordRepository->getLearnWord($request);

        // Если слово найдено
        if ($latestWord) {
            // Если передано предыдущее слово и действие с ним
            if (!is_null($request->last_word_id) && !is_null($request->action_with_word)) {
                // Обновляем метку времени предыдущего слова
                $this->learnWordRepository->updateWordTimestamp($request->last_word_id, $request->action_with_word);
            }

            // Возвращаем найденное слово
            return new ApiResponse($latestWord);
        }

        // Если слова не найдены, возвращаем сообщение об ошибке
        return new ApiResponse(['message' => 'No more words available'], true, 404);
    }
}
