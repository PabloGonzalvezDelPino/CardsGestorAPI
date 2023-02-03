<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\CollectionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('/users')->group(function(){
    Route::post('/login', [UsersController::class, 'login']);
    Route::put('/register', [UsersController::class, 'register']);
    Route::post('/recoverPass', [UsersController::class, 'recoverPass']);
});
Route::prefix('/cards')->group(function(){
    Route::put('/add', [CardsController::class, 'add']);
});
Route::prefix('/collections')->group(function(){
    Route::middleware(['auth:sanctum','administrador'])->put('/add', [CollectionsController::class, 'add']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
