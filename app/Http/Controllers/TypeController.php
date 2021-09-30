<?php

namespace App\Http\Controllers;

use App\Http\Requests\Word\CreateSentenceRequest;
use App\Http\Requests\Word\CreateTypeRequest;
use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\SelectGetPaginateRequest;
use App\Http\Requests\Word\UpdateSentenceRequest;
use App\Http\Requests\Word\UpdateTypeRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Sentence;
use App\Models\Word;
use App\Models\WordType;
use App\Repositories\SentenceRepository;
use App\Repositories\WordRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TypeController extends Controller
{

    public function store(CreateTypeRequest $request) {
        $type = WordType::create($request->validated());

        return new ApiResponse(compact('type'));
    }

    public function update(UpdateTypeRequest $request) {
        $coll = WordType::where('id',$request['id'])
            ->update(Arr::except($request->validated(),'id'));

        return new ApiResponse(compact('coll'));
    }

}
