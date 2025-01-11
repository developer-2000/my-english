<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Artisan;
use \Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\AuthController;
use \App\Http\Middleware\BackupDatabase;
use \App\Http\Controllers\LanguageController;
use \App\Http\Controllers\WordController;
use \App\Http\Controllers\SentenceController;
use \App\Http\Controllers\LearnWordsController;
use \App\Http\Controllers\GeneratingSentencesAiController;

// технический роут /technical/artisan/clear_all
Route::group(['prefix'=>'technical'], function (){
    Route::get('/artisan/clear_all', function() {
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        return "<h1>all_clear</h1>";
    });
});

Route::get('/translations', [LanguageController::class, 'getTranslations'])
    ->name('translations');

// >>> AUTH
Route::group(['prefix'=>'auth'], function (){
    // Маршруты для отображения формы логина и регистрации, и их обработки
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->middleware(['guest'])->name('auth.showLoginForm');
    Route::post('login', [AuthController::class, 'login'])
        ->name('auth.login');
    Route::get('register', [AuthController::class, 'showRegisterForm'])
        ->middleware(['guest'])->name('auth.showRegisterForm');
    Route::post('register', [AuthController::class, 'register'])
        ->name('auth.register');
    Route::get('logout', [AuthController::class, 'logout'])
        ->name('auth.logout');
    // Маршрут для верификации email
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');
});

// >>> Отображаем главную страницу для авторизованных пользователей
// Перенаправляем на страницу логина для неавторизованных пользователей
Route::get('/', function () {

    // Проверяем имеет ли роль 'user' и старше
    if (Auth::check() && Auth::user()->hasRole('user')) {
        return view('index');
    }
    else {
        return redirect("/login");
    }
})->name('index');

// >>> Группа маршрутов, доступных только авторизованным пользователям с ролью 'user' и старше
Route::group(['middleware' => ['auth', 'role:user']], function () {
// 1
    Route::resource('word', WordController::class)->only([
        'index','store'
    ]);
    Route::group(['prefix'=>'word'], function (){
        Route::post('update-word', [WordController::class, 'updateWord']);
        Route::post('delete-word', [WordController::class, 'deleteWord']);
        Route::get('get-present-tense', [WordController::class, 'getPresentTense']);
    });

// 2
    Route::middleware(['throttle:200,1'])->group(function () {
        Route::resource('sentence', SentenceController::class)->only([
            'index', 'store'
        ]);
        Route::post('sentence/update-sentence', [SentenceController::class, 'updateSentence']);
        Route::get('sentence/search-word', [SentenceController::class, 'searchWord']);
        Route::post('sentence/search-sentences', [SentenceController::class, 'searchSentences']);
        Route::post('sentence/bind-checkbox-sound', [SentenceController::class, 'bindCheckboxSound']);
    });
// 3
    Route::post('learn/get-word', [LearnWordsController::class, 'getLearnWord']);
// 4
    Route::post('ai/generate-sentences', [GeneratingSentencesAiController::class, 'generateSentence']);
// 5
    Route::post('/get-languages', [LanguageController::class, 'getLanguages'])
        ->name('get.languages');
// 6
    Route::post('/set-language-learn-user', [LanguageController::class, 'setLearnLanguageUser'])
        ->name('set.language.learn.user');
// 7
    Route::get('/page-list-words', function () {
        return view('index');
    })->middleware(BackupDatabase::class);
    Route::get('/page-word-sentences', function () {
        return view('index');
    })->middleware(BackupDatabase::class);
});

// >>> Error
Route::view('/errors', 'errors')->name('errors');

// >>> Любой другой маршрут перенаправляется ,
Route::any('{all}', function () {
    // на index, если авторизован
    if (Auth::check() && Auth::user()->hasRole('user')) {
        return redirect()->route('index');
    }
    // иначе на login
    else {
        return redirect()->route('auth.showLoginForm');
    }
})->where('all', '.*');

