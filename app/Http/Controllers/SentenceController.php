<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sentence\BindCheckboxSoundRequest;
use App\Http\Requests\Word\CreateSentenceRequest;
use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\SearchWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateSentenceRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Sentence;
use App\Models\SentenceSound;
use App\Models\Word;
use App\Repositories\SentenceRepository;
use App\Repositories\WordRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
    public function update(UpdateSentenceRequest $request): ApiResponse
    {
        $coll = Sentence::where('id',$request['id'])
            ->update(Arr::except($request->validated(),'id'));

        return new ApiResponse(compact('coll'));
    }

    /**
     * Выдает все слова в которых участвует указанный набор символов
     * @param SearchWordRequest $request
     * @return ApiResponse
     */
    public function searchWord(SearchWordRequest $request): ApiResponse
    {
        $coll = Word::where('word', 'like', $request['word'] . '%')
            ->get()
            ->pluck('word');

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
        $sentences = Sentence::where('sentence', 'like', '%' . $request['word'] . '%')
            ->get();

        return new ApiResponse(compact('sentences'));
    }

    public function bindCheckboxSound(BindCheckboxSoundRequest $request): ApiResponse
    {
        if($request['status']){
            SentenceSound::firstOrCreate([
                'sentence_id' => $request['sentence_id']
            ]);
        }
        else{
            SentenceSound::where('sentence_id', $request['sentence_id'])
                ->delete();
        }

        return new ApiResponse([]);
    }
}
