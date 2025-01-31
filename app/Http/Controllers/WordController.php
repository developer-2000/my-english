<?php

namespace App\Http\Controllers;

use App\Http\Requests\LearnWord\GetLearnWordRequest;
use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\EnWord;
use App\Repositories\WordRepository;
use Illuminate\Http\Request;

class WordController extends Controller {
    protected $wordRepository;

    public function __construct() {
        $this->wordRepository = new WordRepository();
    }

    public function index(SelectGetPaginateRequest $request): ApiResponse {
        $words = $this->wordRepository->getWords($request->validated());

        return new ApiResponse($words);
    }

    /**
     * Создает новое слово и добавляет предложения если они сгенерированы
     * @param CreateWordRequest $request
     * @return ApiResponse
     * @throws \Exception
     */
    public function store(CreateWordRequest $request): ApiResponse {
        $coll = $this->wordRepository->createWord($request);

        return new ApiResponse(compact('coll'));
    }

    /**
     * Обновляет слово и добавляет новые предложения если они сгенерированы
     * @param UpdateWordRequest $request
     * @return ApiResponse
     * @throws \Exception
     */
    public function updateWord(UpdateWordRequest $request): ApiResponse {
        $coll = $this->wordRepository->updateWord($request);

        return new ApiResponse(compact('coll'));
    }

    public function deleteWord(DeleteWordRequest $request): ApiResponse {
        $this->wordRepository->deleteWord($request);

        return new ApiResponse([]);
    }

    public function getPresentTense(): ApiResponse {
        $words = EnWord::where('type_id', 4)->where('description', 'like', '%настоящее время%')->get();

        return new ApiResponse($words);
    }

    /**
     * Получить слово для изучения и обновить метку времени предыдущего слова, если необходимо.
     *
     * @param GetLearnWordRequest $request
     * @return ApiResponse
     */
    public function getLearnWord(GetLearnWordRequest $request): ApiResponse {
        // Получаем слово для изучения из репозитория
        $latestWord = $this->wordRepository->getLearnWord($request);

        // Если слово найдено
        if ($latestWord) {
            // Если передано предыдущее слово и действие с ним
            if (!is_null($request->last_word_id) && !is_null($request->action_with_word)) {
                // Обновляем метку времени предыдущего слова
                $this->wordRepository->updateWordTimestamp($request->last_word_id, $request->action_with_word);
            }
            // Возвращаем найденное слово
            return new ApiResponse($latestWord);
        }

        // Если слова не найдены, возвращаем сообщение об ошибке
        return new ApiResponse(['message' => 'No more words available'], true, 404);
    }

}
