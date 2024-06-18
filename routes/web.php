<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Artisan;
use \Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\AuthController;

// технический роут
Route::group(['prefix'=>'technical'], function (){
    Route::get('/artisan/clear_all', function() {
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        return "<h1>all_clear</h1>";
    });
});

Route::get('/', function () {
    if (Auth::check() && Auth::user()->hasRole('user')) {
        return view('index');
    }
    else {
        return redirect()->route('login');
    }
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');

Route::group(['middleware' => ['auth', 'role:user']], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::any('{all}', function(){
        return redirect('/');
    })->where('all', '.*');
});




