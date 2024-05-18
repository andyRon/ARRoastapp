<?php

use App\Http\Controllers\Web\AppController;
use App\Http\Controllers\Web\AuthenticationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AppController::class, 'getApp'])->middleware('auth');

// 登录页面
// guest中间件的用途是登录用户访问该路由会跳转到指定认证后页面，而非登录用户访问才会显示登录页面。
Route::get("/login", [AppController::class, 'getLogin'])->name('login')->middleware('guest');  // TODO guest

// 注册登录认证路由
// {social}代表所使用的OAuth提供方，比如github，Socialite会根据这个参数值去config/services.php中获取对应的OAuth配置信息。
Route::get('/auth/{social}', [AuthenticationController::class, 'getSocialRedirect'])->middleware('guest');
Route::get('/auth/{social}/callback', [AuthenticationController::class, 'getSocialCallback'])->middleware('guest');


Route::get('test', function () {
    return view('app');
});
