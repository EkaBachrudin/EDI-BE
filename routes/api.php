<?php

use App\Http\Controllers\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/blogs', [BlogController::class, 'getAll']);
Route::post('/blog', [BlogController::class, 'create']);
Route::get('/blog/{id}', [BlogController::class, 'getOne']);
Route::put('/blog/{blog}', [BlogController::class, 'update']);
Route::delete('/blog/{id}', [BlogController::class, 'delete']);
Route::get('/categories', [BlogController::class, 'getAllCategories']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
