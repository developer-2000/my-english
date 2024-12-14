<?php

namespace App\Http\Controllers;

use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Test;
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

}
