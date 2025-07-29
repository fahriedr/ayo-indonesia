<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerPositionController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello world!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::group(['prefix' => 'team'], function () {
        Route::get('/', [TeamController::class, 'getAll']);
        Route::get('/{id}', [TeamController::class, 'get']);
        Route::post('/', [TeamController::class, 'create']);
        Route::put('/{id}', [TeamController::class, 'update']);
        Route::delete('/{id}', [TeamController::class, 'delete']);
    });

    Route::group(['prefix' => 'player_position'], function () {
        Route::get('/', [PlayerPositionController::class, 'getAll']);
        Route::post('/', [PlayerPositionController::class, 'create']);
        Route::put('/{id}', [PlayerPositionController::class, 'update']);
        Route::delete('/{id}', [PlayerPositionController::class, 'delete']);

    });

    Route::group(['prefix' => 'player'], function () {

        Route::get('/', [PlayerController::class, 'getAll']);
        Route::get('/{id}', [PlayerController::class, 'get']);
        Route::post('/', [PlayerController::class, 'create']);
        Route::put('/{id}', [PlayerController::class, 'update']);
        Route::delete('/{id}', [PlayerController::class, 'delete']);
    });
    

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::put('/user', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});