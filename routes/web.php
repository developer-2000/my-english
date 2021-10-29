<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//
//    return view('index');
//});

Route::any('{all}', function(){
    return view('index');
})->where('all', '.*');

