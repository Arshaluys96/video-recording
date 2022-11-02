<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/user/dashboard/', [\App\Http\Controllers\User\DashboardController::class, 'index']);
Route::post('/video/', [\App\Http\Controllers\User\DashboardController::class, 'saveVideo']);
Route::post('/get-text-list/', [\App\Http\Controllers\User\DashboardController::class, 'getTextList'])
    ->name('get.text.list');
Route::post('/get-videos/', [\App\Http\Controllers\User\DashboardController::class, 'getVideo'])
    ->name('get.video');
Route::post('/get-single-template', [\App\Http\Controllers\User\DashboardController::class, 'getSingleTemplate'])
    ->name('get.single.template');

