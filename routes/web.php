<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// технический роут
Route::group(['prefix'=>'technical'], function (){
    // /technical/artisan/clear_all
    Route::get('/artisan/clear_all', function() {
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        return "<h1>all_clear</h1>";
    });

});

Route::any('{all}', function(){
    return view('index');
})->where('all', '.*');

