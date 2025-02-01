<?php

namespace App\Http\Controllers;

use App\Http\Requests\LearnWord\GetLearnWordRequest;
use App\Http\Requests\Sentence\BindCheckboxSoundRequest;
use App\Http\Requests\Sentence\GetLearnSentenceRequest;
use App\Http\Requests\Word\CreateSentenceRequest;
use App\Http\Requests\Word\SearchWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateSentenceRequest;
use App\Http\Responses\ApiResponse;
use App\Models\EnSentence;
use App\Models\Test;
use App\Repositories\SentenceRepository;

class SentenceController extends Controller {
    protected $sentenceRepository;

    public function __construct() {
        $this->sentenceRepository = new SentenceRepository();
    }

    /**
     * Общая выборка предложений с указаной сортировкой в пагинации
     * @param SelectGetPaginateRequest $request
     * @return ApiResponse
     */
    public function index(SelectGetPaginateRequest $request): ApiResponse {
        $sentences = $this->sentenceRepository->getSentences($request->validated());

        return new ApiResponse(compact('sentences'));
    }

    /**
     * Записывает новое предложение и слова из предложения с переводом
     * @param CreateSentenceRequest $request
     * @return ApiResponse
     */
    public function store(CreateSentenceRequest $request): ApiResponse {
        $this->sentenceRepository->storeSentences($request->validated());

        return new ApiResponse([]);
    }

    /**
     * Обновляет предложение и его перевод
     * @param UpdateSentenceRequest $request
     * @return ApiResponse
     */
    public function updateSentence(UpdateSentenceRequest $request): ApiResponse {
        $coll = $this->sentenceRepository->updateSentence($request);

        return new ApiResponse(compact('coll'));
    }

    /**
     * Выдает все слова в которых участвует указанный набор символов
     * @param SearchWordRequest $request
     * @return ApiResponse
     */
    public function searchWord(SearchWordRequest $request): ApiResponse {
        $coll = $this->sentenceRepository->searchWord($request);

        $string = implode(" ", $coll->toArray());

        return new ApiResponse(compact('string'));
    }

    /**
     * Выдает все предложения в которых участвует указанное слово
     * @param SearchWordRequest $request
     * @return ApiResponse
     */
    public function searchSentences(SearchWordRequest $request): ApiResponse {
        $sentences = $this->sentenceRepository->searchSentences($request);

        return new ApiResponse(compact('sentences'));
    }

    public function bindCheckboxSound(BindCheckboxSoundRequest $request): ApiResponse {
        $this->sentenceRepository->bindCheckboxSound($request);

        return new ApiResponse([]);
    }

    /**
     * Получить предложение
     *
     */
    public function getLearnSentence(GetLearnSentenceRequest $request): ApiResponse {
        $sentenceId = $request->sentence_id ?? null;
        $action = $request->action ?? null;
        $nextSentence = null;

        // Если передан ID, находим текущее предложение
        if ($sentenceId) {
            $sentence = EnSentence::find($sentenceId);

            if ($sentence) {
                $oldPriority = $sentence->priority; // Запоминаем старый priority

                if ($action === "up") {
                    $maxPriority = EnSentence::max('priority');
                    $sentence->update(['priority' => $maxPriority + 1]);
                } elseif ($action === "down") {
                    $minPriority = EnSentence::min('priority');
                    $sentence->update(['priority' => $minPriority - 1]);
                }

                // Ищем следующее предложение с тем же приоритетом
                $nextSentence = EnSentence::where('priority', $oldPriority)
                    ->where('id', '!=', $sentenceId)
                    ->orderBy('id', 'desc')
                    ->first();

                // Если такого нет, берём ближайшее с меньшим priority
                if (!$nextSentence) {
                    $nextSentence = EnSentence::where('priority', '<', $oldPriority)
                        ->orderBy('priority', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();
                }
            }
        }

        if (!$nextSentence) {
            $nextSentence = EnSentence::orderBy('priority', 'desc')
                ->orderBy('id', 'desc')
                ->first();
        }

        // Если слова не найдены, возвращаем сообщение об ошибке
        return new ApiResponse(compact("nextSentence"));
    }


}
