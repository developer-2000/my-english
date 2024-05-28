<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\WordController;
use \App\Http\Controllers\SentenceController;
use \App\Http\Controllers\TypeController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 1
Route::resource('word', WordController::class)->only([
    'index','store','update'
]);

Route::get('word/test', [WordController::class, 'test']);
Route::post('word/delete-word', [WordController::class, 'deleteWord']);
Route::post('word/delete-word', [WordController::class, 'deleteWord']);
Route::post('word/add-type-another-word', [WordController::class, 'addTypeAnotherWord']);

// 2
Route::resource('sentence', SentenceController::class)->only([
    'index','store','update'
]);
Route::get('sentence/search-word', [SentenceController::class, 'searchWord']);
Route::post('sentence/bind-checkbox-sound', [SentenceController::class, 'bindCheckboxSound']);

// 3
Route::resource('type', TypeController::class)->only([
    'store','update'
]);
