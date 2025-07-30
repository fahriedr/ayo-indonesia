<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerPositionController;
use App\Http\Controllers\RefereeController;
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
    
    Route::group(['prefix' => 'game'], function () {
        Route::get('/', [GameController::class, 'getAll']);
        Route::get('/{id}', [GameController::class, 'get']);
        Route::post('/', [GameController::class, 'create']);
        Route::put('/{id}', [GameController::class, 'update']);
        Route::delete('/{id}', [GameController::class, 'delete']);
    });

    Route::group(['prefix' => 'referee'], function () {
        Route::get('/', [RefereeController::class, 'getAll']);
        Route::get('/{id}', [RefereeController::class, 'get']);
        Route::post('/', [RefereeController::class, 'create']);
        Route::put('/{id}', [RefereeController::class, 'update']);
        Route::delete('/{id}', [RefereeController::class, 'delete']);
    });

    Route::group(['prefix' => 'goal'], function () {
        Route::get('/', [GoalController::class, 'getAll']);
        Route::get('/{id}', [GoalController::class, 'get'])->where('id', '[0-9]+');
        Route::post('/', [GoalController::class, 'create']);
        Route::put('/{id}', [GoalController::class, 'update']);
        Route::delete('/{id}', [GoalController::class, 'delete']);
        Route::get('/top_scorers', [GoalController::class, 'getTopScorers']);
    });

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::put('/user', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});