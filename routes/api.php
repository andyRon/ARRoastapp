<?php

use App\Http\Controllers\API\CafesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 无须登录即可访问
Route::group(['prefix' => 'v1'], function () {

});

Route::group(['prefix' => 'v1'
//    , 'middleware' => 'auth:api'
], function() {
    Route::get('/user', function (Request $r) {
       return $r->user();
    });

    Route::get('/cafes', [CafesController::class, 'getCafes']);
    Route::get('/cafes/{id}', [CafesController::class, 'getCafe']);
    Route::post('/cafes', [CafesController::class, 'postNewCafe']);

});
