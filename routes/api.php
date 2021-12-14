<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ReorderTodoController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/user', UserController::class, 'user');

Route::post('register', [UserController::class , 'register']);
Route::post('login', [UserController::class , 'authenticate']);
Route::get('open', [DataController::class, 'open']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('user', [UserController::class , 'getAuthenticatedUser']);
    Route::get('closed', [DataController::class , 'closed']);
    Route::post('todo', [TodoController::class, 'store']);
    Route::get('todo', [TodoController::class, 'index']);
    Route::get('todo/{id}', [TodoController::class, 'show']);
    Route::put('todo/{id}', [TodoController::class, 'update']);
    Route::delete('todo/{id}', [TodoController::class, 'destroy']);
    Route::patch('reorder', [ReorderTodoController::class, 'update']);
});
