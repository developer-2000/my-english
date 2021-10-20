<?php
namespace App\Http\Controllers;

use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Word;
use App\Repositories\WordRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


class WordController extends Controller
{
    protected $wordRepository;

    public function __construct() {
        $this->wordRepository = new WordRepository();
    }

    public function index(SelectGetPaginateRequest $request) {
        $words = $this->wordRepository->getWords($request->validated());

        return new ApiResponse($words);
    }

    public function store(CreateWordRequest $request) {
        $coll = Word::create($request->validated());

        return new ApiResponse(compact('coll'));
    }

    public function update(UpdateWordRequest $request) {
        $coll = Word::where('id',$request['id'])
            ->update(Arr::except($request->validated(),'id'));

        return new ApiResponse(compact('coll'));
    }

    public function deleteWord(DeleteWordRequest $request) {
        Word::where('id',$request->id)
            ->delete();

        return new ApiResponse([]);
    }
}
