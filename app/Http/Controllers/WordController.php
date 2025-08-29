<?php

namespace App\Http\Controllers;

use App\Http\Requests\LearnWord\GetLearnWordRequest;
use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\EnWord;
use App\Models\Test;
use App\Repositories\WordRepository;

class WordController extends Controller
{
    protected $wordRepository;

    public function __construct()
    {
        $this->wordRepository = new WordRepository;
    }

    /**
     * Выборка в пагинации страницы слов
     *
     * @param SelectGetPaginateRequest $request
     * @return ApiResponse
     */
    public function index(SelectGetPaginateRequest $request): ApiResponse
    {
        $words = $this->wordRepository->getWords($request->validated());

        return new ApiResponse($words);
    }

    /**
     * Создает новое слово и добавляет предложения если они сгенерированы
     *
     * @throws \Exception
     */
    public function store(CreateWordRequest $request): ApiResponse
    {
        $coll = $this->wordRepository->createWord($request);

        return new ApiResponse(compact('coll'));
    }

    /**
     * Обновляет слово и добавляет новые предложения если они сгенерированы
     *
     * @throws \Exception
     */
    public function updateWord(UpdateWordRequest $request): ApiResponse
    {
        $coll = $this->wordRepository->updateWord($request);

        return new ApiResponse(compact('coll'));
    }

    public function deleteWord(DeleteWordRequest $request): ApiResponse
    {
        $this->wordRepository->deleteWord($request);

        return new ApiResponse([]);
    }

    public function getPresentTense(): ApiResponse
    {
        $words = EnWord::where('type_id', 4)->where('description', 'like', '%настоящее время%')->get();

        return new ApiResponse($words);
    }

    /**
     * Получить слово для изучения и обновить статус предыдущего слова.
     *
     * @param GetLearnWordRequest $request
     * @return ApiResponse
     */
    public function getLearnWord(GetLearnWordRequest $request): ApiResponse
    {
        $validateData = $request->validated();

        // Обновляем статус предыдущего слова
        if (
            !empty($validateData['word_id']) &&
            !empty($validateData['status'])
        ) {
            $this->wordRepository->updateWordStatus((int)$validateData['word_id'], $validateData['status']);
        }

        // Получаем следующее слово
        $nextWord = $this->wordRepository->getNextLearnWord($validateData);

        if ($nextWord) {
            return new ApiResponse($nextWord);
        }

        return new ApiResponse(['message' => 'No more words available'], true, 404);
    }


}
