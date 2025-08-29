<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneratingSentencesAiController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SentenceController;
use App\Http\Controllers\TechnicalController;
use App\Http\Controllers\WordController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// технический роут /technical/artisan/clear_all
Route::group(['prefix' => 'technical'], function () {
    Route::get('/artisan/clear_all', function () {
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return '<h1>all_clear</h1>';
    });


    Route::group(['middleware' => ['auth', 'role:admin']], function () {
        Route::get('/page', [TechnicalController::class, 'index']);
        // Сохранить в JSON Правильные глаголы
        Route::get('/regular-verbs', [TechnicalController::class, 'getRegularVerbs']);
        // Сохранить в JSON Неправильные глаголы
        Route::get('/irregular-verbs', [TechnicalController::class, 'getIrregularVerbs']);
        // Сохранить в базу Правильные глаголы
        Route::post('/save-regular-verbs-db', [TechnicalController::class, 'saveRegularVerbsInDatabase']);
        // Сохранить в базу Неправильные глаголы
        Route::post('/save-irregular-verbs-db', [TechnicalController::class, 'saveIrregularVerbsInDatabase']);
    });

});

Route::get('/translations', [LanguageController::class, 'getTranslations'])
    ->name('translations');

// >>> AUTH
Route::group(['prefix' => 'auth'], function () {
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

    // Очищаем любой возможный output buffer
    if (ob_get_level()) {
        ob_clean();
    }

    // Проверяем имеет ли роль 'user' и старше
    if (Auth::check() && Auth::user()->hasRole('user')) {
        return view('index');
    } else {
        return redirect()->route('auth.showLoginForm');
    }
})->middleware(['BackupDatabase'])->name('index');

// >>> Группа маршрутов, доступных только авторизованным пользователям с ролью 'user' и старше
Route::group([], function () {

    // 1 word
    Route::resource('word', WordController::class)->only([
        'index', 'store',
    ])->middleware(['auth', 'role:user']);
    Route::group(['prefix' => 'word', 'middleware' => ['auth', 'role:user']], function () {
        Route::post('update-word', [WordController::class, 'updateWord']);
        Route::post('delete-word', [WordController::class, 'deleteWord']);
        Route::get('get-present-tense', [WordController::class, 'getPresentTense']);
        Route::post('learn/get-word', [WordController::class, 'getLearnWord']);
    });

    // 2 sentence
    Route::middleware(['throttle:200,1', 'auth', 'role:user'])->group(function () {
        Route::resource('sentence', SentenceController::class)->only([
            'index', 'store',
        ]);
        Route::group(['prefix' => 'sentence'], function () {
            Route::post('update-sentence', [SentenceController::class, 'updateSentence']);
            Route::get('search-word', [SentenceController::class, 'searchWord']);
            Route::post('search-sentences', [SentenceController::class, 'searchSentences']);
            Route::post('bind-checkbox-sound', [SentenceController::class, 'bindCheckboxSound']);
            Route::post('learn/get-sentence', [SentenceController::class, 'getLearnSentence']);
        });
    });

    // 4
    Route::post('ai/generate-sentences', [GeneratingSentencesAiController::class, 'generateSentence'])
        ->middleware(['auth', 'role:user']);

    // 5
    Route::post('/get-languages', [LanguageController::class, 'getLanguages'])
        ->name('get.languages')
        ->middleware(['auth', 'role:user']);

    // 6
    Route::post('/set-language-learn-user', [LanguageController::class, 'setLearnLanguageUser'])
        ->name('set.language.learn.user')
        ->middleware(['auth', 'role:user']);

    // 7 - Vue Router маршруты (должны быть защищены авторизацией)
    Route::get('/page-list-words', function () {
        if (! Auth::check()) {
            return redirect()->route('auth.showLoginForm');
        }

        return view('index');
    });
    Route::get('/page-word-sentences', function () {
        if (! Auth::check()) {
            return redirect()->route('auth.showLoginForm');
        }

        return view('index');
    });
//    Route::get('/technical/page', function () {
//        if (! Auth::check()) {
//            return redirect()->route('auth.showLoginForm');
//        }
//
//        return view('index');
//    });
});

// >>> Error
Route::view('/errors', 'errors')->name('errors');

// >>> Любой другой маршрут перенаправляется (кроме API и auth маршрутов)
Route::fallback(function () {
    // Очищаем любой возможный output buffer
    if (ob_get_level()) {
        ob_clean();
    }

    if (Auth::check() && Auth::user()->hasRole('user')) {
        return redirect()->route('index');
    }

    return redirect()->route('auth.showLoginForm');
});
