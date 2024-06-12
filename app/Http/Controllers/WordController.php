<?php
namespace App\Http\Controllers;

use App\Http\Requests\Word\AddTypeAnotherWordRequest;
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

    public function index(SelectGetPaginateRequest $request): ApiResponse
    {
        $words = $this->wordRepository->getWords($request->validated());

        return new ApiResponse($words);
    }

    public function store(CreateWordRequest $request): ApiResponse
    {
        $coll = Word::create($request->validated());

        return new ApiResponse(compact('coll'));
    }

    public function update(UpdateWordRequest $request): ApiResponse
    {
        // Получение экземпляра модели
        $coll = Word::findOrFail($request['id']);

        // Временное отключение обновления 'updated_at'
        $coll->timestamps = false;

        // Обновление данных, за исключением столбца 'id'
        $coll->fill($request->validated())->save();

        // Включение обновления 'updated_at'
        $coll->timestamps = true;

        return new ApiResponse(compact('coll'));
    }

    public function deleteWord(DeleteWordRequest $request): ApiResponse
    {
        Word::where('id',$request->id)
            ->delete();

        return new ApiResponse([]);
    }

    public function addTypeAnotherWord(AddTypeAnotherWordRequest $request): ApiResponse
    {
        $collection_from_word = Word::where('id',$request->from_word_id)->first();
        Word::where('word',$request->to_word_text)
            ->update([
                'type_id' => $collection_from_word->type_id,
                'time_forms' => $collection_from_word->time_forms,
            ]);

        return new ApiResponse([]);
    }
}
