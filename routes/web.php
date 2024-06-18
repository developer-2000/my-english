<?php

use App\Models\WordType;
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

//    $collection = WordType::where('id',30)->first();
//    dd( $collection->description['object']['past'] );
//    dd( $collection->description['text'] );

//    $collection = WordType::where('id', '>=', 13)
//        ->where('description->text', '!=', null)
//        ->get();
//
//    dd( $collection );


    return view('index');
})->where('all', '.*');

