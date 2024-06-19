<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Artisan;
use \Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\AuthController;

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

// >>> AUTH
// Маршруты для отображения формы логина и регистрации, и их обработки
Route::get('login', [AuthController::class, 'showLoginForm'])
    ->middleware(['guest'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegisterForm'])
    ->middleware(['guest'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('logout', [AuthController::class, 'logout'])
    ->name('logout');
// Маршрут для верификации email
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');

// Отображаем главную страницу для авторизованных пользователей
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

// Группа маршрутов, доступных только авторизованным пользователям с ролью 'user' и старше
//Route::group(['middleware' => ['auth', 'role:user']], function () {
//
//});

// Любой другой маршрут перенаправляется ,
Route::any('{all}', function () {
    // на index, если авторизован
    if (Auth::check() && Auth::user()->hasRole('user')) {
        return redirect()->route('index');
    }
    // иначе на login
    else {
        return redirect()->route('login');
    }
})->where('all', '.*');


