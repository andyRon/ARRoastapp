<?php

use App\Http\Controllers\API\BrewMethodsController;
use App\Http\Controllers\API\CafesController;
use App\Http\Controllers\API\TagsController;
use App\Http\Controllers\API\UsersController;
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
    Route::get('/user', [UsersController::class, 'getUser']);

    Route::get('/cafes', [CafesController::class, 'getCafes']);
    Route::get('/cafes/{id}', [CafesController::class, 'getCafe']);

    Route::get('/brew-methods', [BrewMethodsController::class, 'getBrewMethods']);

    Route::get('/tags', [TagsController::class, 'getTags']);

});

Route::group(['prefix' => 'v1'
//    , 'middleware' => 'auth:api'
], function() {
    Route::get('/user', function (Request $r) {
       return $r->user();
    });

    Route::post('/cafes', [CafesController::class, 'postNewCafe']);

    Route::post('/cafes/{id)/like',[CafesController::class, 'postLikeCafe']);
    Route::delete('/cafes/{id)/like',[CafesController::class, 'deleteLikeCafe']);

    Route::post('/cafes/{id}/tags', [CafesController::class, 'postAddTags']);
    Route::delete('/cafes/{id}/tags/{tagID}', [CafesController::class, 'deleteCafeTag']);

    Route::put('/user', [UsersController::class, 'putUpdateUser']);
});
