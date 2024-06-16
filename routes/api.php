<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\WordController;
use \App\Http\Controllers\SentenceController;
use \App\Http\Controllers\LearnWordsController;
use \App\Http\Controllers\GeneratingSentencesAiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 1
Route::resource('word', WordController::class)->only([
    'index','store','update'
]);
Route::post('word/delete-word', [WordController::class, 'deleteWord']);

// 2
Route::middleware(['throttle:200,1'])->group(function () {
    Route::resource('sentence', SentenceController::class)->only([
        'index', 'store', 'update'
    ]);
    Route::get('sentence/search-word', [SentenceController::class, 'searchWord']);
    Route::post('sentence/search-sentences', [SentenceController::class, 'searchSentences']);
    Route::post('sentence/bind-checkbox-sound', [SentenceController::class, 'bindCheckboxSound']);
});

// 3
Route::post('learn/get-word', [LearnWordsController::class, 'getLearnWord']);

// 4
Route::post('ai/generate-sentences', [GeneratingSentencesAiController::class, 'generateSentence']);
