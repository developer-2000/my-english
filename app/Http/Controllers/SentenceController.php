<?php

namespace App\Http\Controllers;

use App\Http\Requests\Word\CreateSentenceRequest;
use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\SearchWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateSentenceRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Sentence;
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

    public function index(SelectGetPaginateRequest $request) {
        $sentences = $this->sentenceRepository->getSentences($request->validated());

        return new ApiResponse(compact('sentences'));
    }

    public function store(CreateSentenceRequest $request) {
        $sentence = $this->sentenceRepository->storeSentences($request->validated());

        return new ApiResponse(compact('sentence'));
    }

    public function update(UpdateSentenceRequest $request) {
        $coll = Sentence::where('id',$request['id'])
            ->update(Arr::except($request->validated(),'id'));

        return new ApiResponse(compact('coll'));
    }

    public function searchWord(SearchWordRequest $request) {
        $coll = Word::where('word', 'like', $request['word'] . '%')
            ->get()
            ->pluck('word');

        $string = implode(" ", $coll->toArray());

        return new ApiResponse(compact('string'));
    }
}
