<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sentence\BindCheckboxSoundRequest;
use App\Http\Requests\Word\CreateSentenceRequest;
use App\Http\Requests\Word\SearchWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateSentenceRequest;
use App\Http\Responses\ApiResponse;
use App\Repositories\SentenceRepository;

class SentenceController extends Controller
{
    protected $sentenceRepository;

    public function __construct() {
        $this->sentenceRepository = new SentenceRepository();
    }

    /**
     * Общая выборка предложений с указаной сортировкой в пагинации
     * @param SelectGetPaginateRequest $request
     * @return ApiResponse
     */
    public function index(SelectGetPaginateRequest $request): ApiResponse
    {
        $sentences = $this->sentenceRepository->getSentences($request->validated());

        return new ApiResponse(compact('sentences'));
    }

    /**
     * Записывает новое предложение и слова из предложения с переводом
     * @param CreateSentenceRequest $request
     * @return ApiResponse
     */
    public function store(CreateSentenceRequest $request): ApiResponse
    {
        $this->sentenceRepository->storeSentences($request->validated());

        return new ApiResponse([]);
    }

    /**
     * Обновляет предложение и его перевод
     * @param UpdateSentenceRequest $request
     * @return ApiResponse
     */
    public function updateSentence(UpdateSentenceRequest $request): ApiResponse
    {
        $coll = $this->sentenceRepository->updateSentence($request);

        return new ApiResponse(compact('coll'));
    }

    /**
     * Выдает все слова в которых участвует указанный набор символов
     * @param SearchWordRequest $request
     * @return ApiResponse
     */
    public function searchWord(SearchWordRequest $request): ApiResponse
    {
        $coll = $this->sentenceRepository->searchWord($request);

        $string = implode(" ", $coll->toArray());

        return new ApiResponse(compact('string'));
    }

    /**
     * Выдает все предложения в которых участвует указанное слово
     * @param SearchWordRequest $request
     * @return ApiResponse
     */
    public function searchSentences(SearchWordRequest $request): ApiResponse
    {
        $sentences = $this->sentenceRepository->searchSentences($request);

        return new ApiResponse(compact('sentences'));
    }

    public function bindCheckboxSound(BindCheckboxSoundRequest $request): ApiResponse
    {
        $this->sentenceRepository->bindCheckboxSound($request);

        return new ApiResponse([]);
    }
}
