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
    Route::post('/login', [UsersController::class, 'login']);//->name('login');
    Route::put('/register', [UsersController::class, 'register']);
    Route::post('/recoverPass', [UsersController::class, 'recoverPass']);
});
Route::prefix('/cards')->group(function(){
    Route::middleware(['auth:sanctum','ability:Administrador'])->put('/create', [CardsController::class, 'create']);
    Route::middleware(['auth:sanctum','ability:Administrador'])->put('/addToCollection', [CardsController::class, 'addToCollection']);
    Route::middleware(['auth:sanctum','ability:Particular,Profesional'])->get('/searchByName', [CardsController::class, 'searchByName']);
    Route::middleware(['auth:sanctum','ability:Particular,Profesional'])->post('/publishCard', [CardsController::class, 'publishCard']);
    Route::get('/searchToBuy', [CardsController::class, 'searchToBuy']);
    Route::middleware(['auth:sanctum','ability:Administrador'])->post('/edit', [CardsController::class, 'edit']);
});
Route::prefix('/collections')->group(function(){
    Route::middleware(['auth:sanctum','ability:Administrador'])->put('/create', [CollectionsController::class, 'create']);
    Route::middleware(['auth:sanctum','ability:Administrador'])->post('/edit', [CollectionsController::class, 'edit']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
