<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TechnicalController;
use App\Http\Controllers\WordController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Технические роуты для работы с глаголами
Route::post('/technical/save-irregular-verbs-db', [TechnicalController::class, 'saveIrregularVerbsInDatabase']);


